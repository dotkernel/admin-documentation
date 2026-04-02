<?php

declare(strict_types=1);

use Core\Admin\Entity\Admin;
use Core\Admin\Entity\AdminIdentity;

return [
    'dot_totp' => [
        'identity_class_map'   => [
            AdminIdentity::class => Admin::class,
        ],
        'totp_required_routes' => [
            'page::components' => true,
        ],
        'options'              => [
            // Time step in seconds
            'period' => 30,
            // Number of digits in the TOTP code
            'digits' => 6,
            // Hashing algorithm used to generate the code
            'algorithm' => 'sha1',
        ],
        'provision_uri_config' => [
            'issuer' => 'ALX-Admin',
        ],
    ],
];
