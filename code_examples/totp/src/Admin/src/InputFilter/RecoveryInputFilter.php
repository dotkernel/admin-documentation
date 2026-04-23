<?php

declare(strict_types=1);

namespace Admin\Admin\InputFilter;

use Admin\App\InputFilter\Input\CsrfInput;
use Core\App\InputFilter\AbstractInputFilter;

/**
 * @phpstan-type RecoveryDataType array{
 *     code: non-empty-string,
 *     totpCsrf: non-empty-string,
 *     submit?: non-empty-string,
 * }
 * @extends AbstractInputFilter<RecoveryDataType>
 */
class RecoveryInputFilter extends AbstractInputFilter
{
    public function init(): void
    {
        $this->add([
            'name'       => 'recoveryCode',
            'required'   => true,
            'filters'    => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'Regex',
                    'options' => [
                        'pattern' => '/^[A-Z0-9]{5}-[A-Z0-9]{5}$/',
                        'message' => 'Recovery code must be in format XXXXX-XXXXX using letters A-Z and digits 0-9.',
                    ],
                ],
            ],
        ]);

        $this->add(new CsrfInput('recoveryCsrf'));
    }
}
