<?php

declare(strict_types=1);

namespace Admin\Admin\Handler\Account;

use Admin\Admin\Form\TotpForm;
use Admin\Admin\Service\AdminServiceInterface;
use Admin\App\Exception\NotFoundException;
use Core\App\Message;
use Dot\DependencyInjection\Attribute\Inject;
use Dot\FlashMessenger\FlashMessengerInterface;
use Dot\Totp\Totp;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Exception\ExceptionInterface;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Random\RandomException;

use function is_array;

class PostEnableTotpHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        TemplateRendererInterface::class,
        AdminServiceInterface::class,
        AuthenticationService::class,
        Totp::class,
        FlashMessengerInterface::class,
        TotpForm::class
    )]
    public function __construct(
        protected RouterInterface $router,
        protected TemplateRendererInterface $template,
        protected AdminServiceInterface $adminService,
        protected AuthenticationService $authenticationService,
        protected Totp $totpService,
        protected FlashMessengerInterface $messenger,
        protected TotpForm $form
    ) {
    }

    /**
     * @throws ExceptionInterface
     * @throws RandomException
     */
    public function handle(ServerRequestInterface $request): EmptyResponse|RedirectResponse|HtmlResponse
    {
        try {
            $admin = $this->adminService->findAdmin($this->authenticationService->getIdentity()->getId());
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $parsedBody = $request->getParsedBody();
        $storage    = $this->authenticationService->getStorage()->read();

        if (! is_array($parsedBody) || ! isset($parsedBody['code'])) {
            $this->messenger->addError(Message::VALIDATOR_INVALID_CODE);
            return new RedirectResponse(
                $this->router->generateUri('admin::validate-totp-form')
            );
        }

        $code = (string) $parsedBody['code'];

        if ($admin->isTotpEnabled()) {
            $pendingSecret = $admin->getTotpSecret();
        } else {
            $pendingSecret = $storage->pendingSecret;
        }

        if ($this->totpService->verifyCode($pendingSecret, $code)) {
            $recoveryCodes = $this->totpService->generateRecoveryCodes();
            $admin->enableTotp($pendingSecret);

            $hashedRecoveryCodes = $this->totpService->hashRecoveryCodes($recoveryCodes);
            $admin->setRecoveryCodes($hashedRecoveryCodes);

            $this->adminService->getAdminRepository()->saveResource($admin);
            $storage->totp_verified = true;
            if (isset($storage->recovery_auth)) {
                unset($storage->recovery_auth);
            }

            return new HtmlResponse($this->template->render('admin::list-recovery-codes', [
                'totpForm'   => $this->form,
                'plainCodes' => $recoveryCodes,
            ]));
        } else {
            $this->messenger->addError(Message::VALIDATOR_INVALID_CODE);
        }

        return new RedirectResponse($this->router->generateUri('admin::enable-totp-form'));
    }
}
