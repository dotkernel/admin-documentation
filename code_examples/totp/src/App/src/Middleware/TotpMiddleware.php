<?php

declare(strict_types=1);

namespace Admin\App\Middleware;

use Core\Admin\Entity\Admin;
use Core\Admin\Entity\AdminIdentity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Exception\ExceptionInterface;
use Laminas\Diactoros\Response\RedirectResponse;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function in_array;

class TotpMiddleware implements MiddlewareInterface
{
    /** @var array<string, bool> */
    private array $routes;

    /**
     * @param array<non-empty-string, mixed> $config
     */
    public function __construct(
        protected AuthenticationService $authService,
        protected EntityManagerInterface $entityManager,
        protected RouterInterface $router,
        protected array $config
    ) {
        $this->routes = $config['dot_totp']['totp_required_routes'] ?? [];
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws ExceptionInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): responseInterface|RedirectResponse {
        $storage = $this->authService->getStorage()->read();
        /** @var AdminIdentity $identity */
        $identity    = $this->authService->getIdentity();
        $routeResult = $request->getAttribute(RouteResult::class);
        $routeName   = $routeResult->getMatchedRouteName();

        $excludedRoutes = [
            'admin::validate-totp-form',
            'admin::validate-totp',
            'admin::enable-totp-form',
            'admin::logout-admin',
            'admin::recovery-form',
            'admin::validate-recovery',
            'admin::login-admin-form',
        ];

        if (
            in_array($routeName, $excludedRoutes, true) ||
            (isset($storage->totp_verified) && $storage->totp_verified)
        ) {
            return $handler->handle($request);
        }

        if ($identity instanceof UserInterface) {
            $type = $identity::class;
            $map  = $this->config['dot_totp']['identity_class_map'];

            if (! isset($map[$type])) {
                throw new RuntimeException("No identity_class configured for type $type");
            }

            /** @var class-string<object> $entityClass */
            $entityClass = $map[$type];
            /** @var Admin|null $entity */
            $entity = $this->entityManager->find($entityClass, $identity->id);

            if ($entity === null) {
                throw new RuntimeException("Entity of type $entityClass with ID {$identity->id} not found");
            }

            if (! isset($storage->totp_verified) && $entity->isTotpEnabled()) {
                return new RedirectResponse($this->router->generateUri('admin::validate-totp-form'));
            }

            if (! isset($this->routes[$routeName]) || ! $this->routes[$routeName]) {
                return $handler->handle($request);
            }

            $storage->route = $routeName;
            if ($entity->isTotpEnabled()) {
                return new RedirectResponse($this->router->generateUri('admin::validate-totp-form'));
            } else {
                return new RedirectResponse($this->router->generateUri('admin::enable-totp-form'));
            }
        }

        return $handler->handle($request);
    }
}