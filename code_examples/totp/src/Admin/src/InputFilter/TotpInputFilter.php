<?php

declare(strict_types=1);

namespace Admin\Admin\InputFilter;

use Admin\App\InputFilter\Input\CsrfInput;
use Core\App\InputFilter\AbstractInputFilter;
use Laminas\Validator\Digits;
use Laminas\Validator\StringLength;

/**
 * @phpstan-type TotpDataType array{
 *     code: non-empty-string,
 *     totpCsrf: non-empty-string,
 *     submit?: non-empty-string,
 * }
 * @extends AbstractInputFilter<TotpDataType>
 */
class TotpInputFilter extends AbstractInputFilter
{
    public function init(): void
    {
        $this->add([
            'name'       => 'code',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => Digits::class,
                    'options' => [
                        'message' => 'Code must contain only digits.',
                    ],
                ],
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min'     => 6,
                        'max'     => 6,
                        'message' => 'Code must be exactly 6 digits.',
                    ],
                ],
            ],
        ]);

        $this->add(new CsrfInput('totpCsrf'));
    }
}
