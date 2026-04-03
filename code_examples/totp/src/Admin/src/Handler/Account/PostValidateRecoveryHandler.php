<?php

declare(strict_types=1);

namespace Admin\Admin\Handler\Account;

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
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function array_values;
use function is_array;

class PostValidateRecoveryHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        AdminServiceInterface::class,
        AuthenticationService::class,
        Totp::class,
        FlashMessengerInterface::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected AdminServiceInterface $adminService,
        protected AuthenticationService $authenticationService,
        protected Totp $totpService,
        protected FlashMessengerInterface $messenger,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function handle(ServerRequestInterface $request): EmptyResponse|RedirectResponse
    {
        try {
            $admin = $this->adminService->findAdmin($this->authenticationService->getIdentity()->getId());
        } catch (NotFoundException $exception) {
            $this->messenger->addError($exception->getMessage());

            return new EmptyResponse(StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $parsedBody = $request->getParsedBody();
        $storage    = $this->authenticationService->getStorage()->read();

        if (! is_array($parsedBody) || ! isset($parsedBody['recoveryCode'])) {
            $this->messenger->addError(Message::VALIDATOR_INVALID_CODE);
            return new RedirectResponse(
                $this->router->generateUri('admin::recovery-form')
            );
        }

        $recoveryCode = (string) $parsedBody['recoveryCode'];
        $hashedCodes  = $admin->getRecoveryCodes() ?? [];

        if ($this->totpService->validateRecoveryCode($recoveryCode, $hashedCodes)) {
            $admin->setRecoveryCodes(array_values($hashedCodes));
            $admin->disableTotp();
            $storage->recovery_auth = true;

            $this->adminService->getAdminRepository()->saveResource($admin);
            return new RedirectResponse($this->router->generateUri('admin::enable-totp-form'));
        } else {
            $this->messenger->addError(Message::VALIDATOR_INVALID_CODE);
        }

        return new RedirectResponse($this->router->generateUri('admin::validate-totp-form'));
    }
}
