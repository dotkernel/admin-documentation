# Authorization Guards

## Summary

This guide explains how [dot-rbac-guard](https://github.com/dotkernel/dot-rbac-guard) and [dot-rbac](https://github.com/dotkernel/dot-rbac) restrict access to parts of Dotkernel Admin, and how the `authorization.global.php` and `authorization-guards.global.php` files define roles, permissions and route-level access rules.

## Details

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

The `authorization-guards.global.php` file defines which permissions are required to access specific route handlers.
These permissions must first be declared in the `authorization.global.php` (dot-rbac) configuration file.

```php
// Example configuration granting access to route handlers based on permissions.
    'rules' => [
        'admin::admin-login-form'        => [],
        'admin::admin-login'             => [],
        'admin::admin-create-form'       => ['authenticated'],
        'admin::admin-create'            => ['authenticated'],
        'admin::admin-delete-form'       => ['authenticated'],
        'admin::admin-delete'            => ['authenticated'],
        'admin::admin-edit-form'         => ['authenticated'],
        'admin::admin-edit'              => ['authenticated'],
    ]
```

## FAQ

**Q: Which packages handle authorization in Dotkernel Admin?**

A: `dot-rbac-guard` and `dot-rbac` work together to restrict access to parts of the application based on the user's role.

**Q: Where are roles and their permissions defined?**

A: Roles and the permissions they grant are defined in the `authorization.global.php` configuration file.

**Q: Where do I configure which permissions a route requires?**

A: Route-level access is configured in `authorization-guards.global.php`, using permissions that are already declared in `authorization.global.php`.
