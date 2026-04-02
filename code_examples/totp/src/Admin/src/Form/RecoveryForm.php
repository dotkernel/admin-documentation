<?php

declare(strict_types=1);

namespace Admin\Admin\Form;

use Admin\Admin\InputFilter\RecoveryInputFilter;
use Admin\App\Form\AbstractForm;
use Dot\DependencyInjection\Attribute\Inject;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Submit;
use Laminas\Form\Element\Text;
use Laminas\Form\Exception\ExceptionInterface;
use Laminas\Session\Container;
use Mezzio\Router\RouterInterface;

/**
 * @phpstan-import-type RecoveryDataType from RecoveryInputFilter
 * @extends AbstractForm<RecoveryDataType>
 */
class RecoveryForm extends AbstractForm
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

        $this->setAttribute('id', 'recovery-form');
        $this->setAttribute('method', 'post');

        $this->inputFilter = new RecoveryInputFilter();
        $this->inputFilter->init();
    }

    /**
     * @throws ExceptionInterface
     */
    public function init(): void
    {
        $this->add(
            (new Text('recoveryCode'))
                ->setLabel('Recovery Code')
                ->setAttribute('class', 'form-control')
                ->setAttribute('minlength', 11)
                ->setAttribute('maxlength', 11)
                ->setAttribute('pattern', '[A-Za-z0-9]{5}-[A-Za-z0-9]{5}')
                ->setAttribute('required', true)
                ->setAttribute('autofocus', true)
        );

        $this->add(
            (new Csrf('recoveryCsrf'))
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
