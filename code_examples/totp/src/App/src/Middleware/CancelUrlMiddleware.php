<?php

declare(strict_types=1);

namespace Admin\App\Middleware;

use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Exception\ExceptionInterface;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function str_contains;

class CancelUrlMiddleware implements MiddlewareInterface
{
    #[Inject(
        RouterInterface::class,
        AuthenticationService::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected AuthenticationService $authService
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cancelUrl = $this->router->generateUri('admin::login-admin-form');

        $storage = $this->authService->getStorage()->read();

        if (isset($storage->recovery_auth) || isset($storage->totp_verified)) {
            $referer = $request->getHeaderLine('Referer');

            if (str_contains($referer, $this->router->generateUri('admin::recovery-form'))) {
                $cancelUrl = $this->router->generateUri('admin::edit-account-form');
            } else {
                $cancelUrl = $referer;
            }
        }

        return $handler->handle(
            $request->withAttribute('cancelUrl', $cancelUrl)
        );
    }
}
