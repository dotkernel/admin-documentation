<?php

declare(strict_types=1);

namespace Admin\Admin\Form;

use Admin\Admin\InputFilter\TotpInputFilter;
use Admin\App\Form\AbstractForm;
use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;
use Mezzio\Router\RouterInterface;

/**
 * @phpstan-import-type TotpDataType from TotpInputFilter
 * @extends AbstractForm<TotpDataType>
 */
class TotpForm extends AbstractForm
{
    /**
     * @param array<non-empty-string, mixed> $options
     * @throws ExceptionInterface
     */
    #[Inject(
        RouterInterface::class,
    )]
    public function __construct(?string $name = null, array $options = [])
    {
        parent::__construct($name, $options);

        $this->init();

        $this->setAttribute('id', 'enable-totp-form');
        $this->setAttribute('method', 'post');
        $this->setAttribute('title', 'TOTP Authentication Setup');

        $this->inputFilter = new TotpInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        $this->add(
            (new Text('code'))
                ->setLabel('Authentication Code')
                ->setAttribute('class', 'form-control')
                ->setAttribute('maxlength', 6)
                ->setAttribute('pattern', '\d{6}')
                ->setAttribute('required', true)
                ->setAttribute('autofocus', true)
        );

        $this->add(
            (new Csrf('totpCsrf'))
                ->setOptions([
                    'csrf_options' => [
                        'timeout' => 3600,
                        'session' => new Container(),
                    ],
                ])
                ->setAttribute('required', true)
        );

        $this->add(
            (new Submit('submit'))
                ->setAttribute('type', 'submit')
                ->setAttribute('value', 'Verify Code')
                ->setAttribute('class', 'btn btn-primary mt-2')
        );
    }
}
