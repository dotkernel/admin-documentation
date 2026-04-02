<?php

declare(strict_types=1);

namespace Admin\Admin\Handler\Account;

use Admin\Admin\Form\RecoveryForm;
use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GetRecoveryFormHandler implements RequestHandlerInterface
{
    #[Inject(
        TemplateRendererInterface::class,
        RecoveryForm::class,
        RouterInterface::class
    )]
    public function __construct(
        protected TemplateRendererInterface $template,
        protected RecoveryForm $recoveryForm,
        protected RouterInterface $router
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface|EmptyResponse|HtmlResponse
    {
        $this->recoveryForm
            ->setAttribute('action', $this->router->generateUri('admin::validate-recovery'));

        return new HtmlResponse(
            $this->template->render('admin::recovery-form', [
                'recoveryForm' => $this->recoveryForm,
                'cancelUrl'    => $request->getAttribute('cancelUrl'),
            ])
        );
    }
}