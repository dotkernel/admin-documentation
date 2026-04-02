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

use function is_array;

class PostDisableTotpHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        AdminServiceInterface::class,
        AuthenticationService::class,
        Totp::class,
        FlashMessengerInterface::class
    )]
    public function __construct(
        protected RouterInterface $router,
        protected AdminServiceInterface $adminService,
        protected AuthenticationService $authenticationService,
        protected Totp $totpService,
        protected FlashMessengerInterface $messenger
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

        if (! is_array($parsedBody) || ! isset($parsedBody['code'])) {
            $this->messenger->addError(Message::VALIDATOR_INVALID_CODE);
            return new RedirectResponse(
                $this->router->generateUri('admin::validate-totp-form')
            );
        }

        $code = (string) $parsedBody['code'];

        if ($this->totpService->verifyCode((string) $admin->getTotpSecret(), $code)) {
            $admin->disableTotp();
            $this->adminService->getAdminRepository()->saveResource($admin);
            $storage->totp_verified = false;
            return new RedirectResponse($this->router->generateUri('admin::edit-account'));
        } else {
            $this->messenger->addError(Message::VALIDATOR_INVALID_CODE);
        }

        return new RedirectResponse($this->router->generateUri('admin::disable-totp-form'));
    }
}
