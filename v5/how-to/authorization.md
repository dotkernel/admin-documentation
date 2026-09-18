# Authorization Guards

The packages responsible for restricting access to certain parts of the application are [dot-rbac-guard](https://github.com/dotkernel/dot-rbac-guard) and [dot-rbac](https://github.com/dotkernel/dot-rbac).
These packages work together to create an infrastructure that is customizable and diversified to manage user access to the platform by specifying the type of role the user has.

The `authorization.global.php` file provides multiple configurations specifying multiple roles as well as the types of permissions to which these roles have access.

```php
//example of a flat RBAC model that specifies two types of roles as well as their permission
    'roles' => [
                'admin' => [
                    'permissions' => [
                        'authenticated',
                        'edit',
                        'delete',
                        //etc..
                    ]
                ],
                'user' => [
                    'permissions' => [
                        'authenticated',
                        //etc..
                    ]
                ]
            ]
```

The `authorization-guards.global.php` file provides configuration to restrict access to certain actions based on the permissions defined in `authorization.global.php` so basically we have to add the permissions in the dot-rbac configuration file first to specify the action restriction permissions.

```php
// configuration example to restrict certain actions of some routes based on the permissions specified in the dot-rbac configuration file
    'rules' => [
                    [
                        'route' => 'account',
                        'actions' => [//list of actions to apply , or empty array for all actions
                            'unregister',
                            'avatar',
                            'details',
                            'changePassword'
                        ],
                        'permissions' => ['authenticated']
                    ],
                    [
                        'route' => 'admin',
                        'actions' => [
                            'deleteAccount'
                        ],
                         'permissions' => [
                            'delete'
                            //list of roles to allow
                        ]
                    ]
                ]
```
