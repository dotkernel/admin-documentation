<?php

declare(strict_types=1);

namespace Admin\Admin\Handler\Account;

use Admin\Admin\Form\TotpForm;
use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Authentication\AuthenticationService;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetDisableTotpFormHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateRendererInterface::class,
        TotpForm::class,
        RouterInterface::class,
        AuthenticationService::class,
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected TotpForm $totpForm,
        protected RouterInterface $router,
        protected AuthenticationService $authenticationService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface|EmptyResponse|HtmlResponse
    {
        $this->totpForm
            ->setAttribute('action', $this->router->generateUri('admin::disable-totp'));

        return new HtmlResponse(
            $this->template->render('admin::validate-totp-form', [
                'totpForm'  => $this->totpForm->prepare(),
                'cancelUrl' => $this->router->generateUri('admin::edit-account'),
                'error'     => null,
            ])
        );
    }
}