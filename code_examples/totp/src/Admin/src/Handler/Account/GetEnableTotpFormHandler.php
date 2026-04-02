<?php

declare(strict_types=1);

namespace Admin\Admin\Handler\Account;

use Admin\Admin\Form\TotpForm;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Totp\Totp;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Exception\ExceptionInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Random\RandomException;

use function time;

class GetEnableTotpFormHandler implements RequestHandlerInterface
{
    private const int SECRET_MAX_AGE = 600;

    /**
     * @param array{label: string, issuer: string} $provisioningUri
     */
    #[Inject(
        Totp::class,
        AuthenticationService::class,
        FlashMessengerInterface::class,
        TemplateRendererInterface::class,
        TotpForm::class,
        RouterInterface::class,
        'config.dot_totp.provision_uri_config'
    )]
    public function __construct(
        protected Totp $totpService,
        protected AuthenticationService $authenticationService,
        protected FlashMessengerInterface $messenger,
        protected TemplateRendererInterface $template,
        protected TotpForm $totpForm,
        protected RouterInterface $router,
        protected array $provisioningUri
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws RandomException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface|EmptyResponse|HtmlResponse
    {
        $storage = $this->authenticationService->getStorage()->read();

        if (
            empty($storage->pendingSecret) ||
            empty($storage->secretTimestamp) ||
            (time() - $storage->secretTimestamp) > self::SECRET_MAX_AGE
        ) {
            $storage->pendingSecret   = $this->totpService->generateSecretBase32();
            $storage->secretTimestamp = time();
            $this->authenticationService->getStorage()->write($storage);
        }

        $uri = $this->totpService->getProvisioningUri(
            $storage->getIdentity(),
            $this->provisioningUri['issuer'],
            $storage->pendingSecret
        );

        $qrSvg                  = $this->totpService->generateInlineSvgQr($uri);
        $storage->totp_verified = false;

        if (isset($storage->recovery_auth) && $storage->recovery_auth) {
            $this->totpForm->setAttribute('title', 'Reconfigure Two-Factor Authentication');
        }

        $this->totpForm->setAttribute('action', $this->router->generateUri('admin::enable-totp'));

        return new HtmlResponse(
            $this->template->render('admin::validate-totp-form', [
                'qrSvg'     => $qrSvg,
                'cancelUrl' => $request->getAttribute('cancelUrl'),
                'totpForm'  => $this->totpForm->prepare(),
                'error'     => null,
            ])
        );
    }
}
