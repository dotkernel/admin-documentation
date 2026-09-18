# Dependency Injection

## Summary

This page explains how Dotkernel Admin uses the `dot-dependency-injection` package to perform constructor injection via the `#[Inject]` attribute, and how to register a class in the `ConfigProvider` so its dependencies are resolved.

## Details

Dependency injection is a design pattern used in software development to implement inversion of control.
In simpler terms, it's the act of providing dependencies for an object during instantiation.

In PHP, dependency injection can be implemented in various ways, including through constructor injection, setter injection and property injection.

Dotkernel Admin, through its [dot-dependency-injection](https://github.com/dotkernel/dot-dependency-injection) package, focuses only on constructor injection.

## Usage

Dotkernel Admin comes out of the box with the [dot-dependency-injection](https://github.com/dotkernel/dot-dependency-injection) package, which provides all the functionality for injecting dependencies into any object you want.

`dot-dependency-injection` determines the dependencies by looking at the `#[Inject]` attribute, added to the constructor of a class.
Each dependency is specified as a separate parameter of the `#[Inject]` attribute.

For our example we will inject `RouterInterface` and `AuthenticationServiceInterface` dependencies into `GetAccountLogoutHandler`.

```php
use Dot\DependencyInjection\Attribute\Inject;

class GetAccountLogoutHandler implements RequestHandlerInterface
{
    #[Inject(
        RouterInterface::class,
        AuthenticationServiceInterface::class,
    )]
    public function __construct(
        protected RouterInterface $router,
        protected AuthenticationServiceInterface $authenticationService,
    ) {
    }
}
```

> If your class needs the value of a specific configuration key, you can specify the path using dot notation: `config.example`

The next step is to register the class in the `ConfigProvider` under `factories` using `Dot\DependencyInjection\Factory\AttributedServiceFactory::class`.

```php
public function getDependencies(): array
{
    return [
        'factories' => [
            GetAccountLogoutHandler::class => AttributedServiceFactory::class,
        ],
    ];
}
```

That's it.
When your object is instantiated from the container, it will automatically have its dependencies resolved.

> Dependencies injection is available to any object within Dotkernel Admin.
> For example, you can inject dependencies in a service, a handler and so on, simply by registering them in the `ConfigProvider`.

## FAQ

**Q: Which type of dependency injection does Dotkernel Admin support?**

A: Through the `dot-dependency-injection` package, Dotkernel Admin focuses only on constructor injection.

**Q: How does `dot-dependency-injection` know which dependencies to inject?**

A: It reads the `#[Inject]` attribute added to a class's constructor, where each dependency is listed as a separate parameter.

**Q: How do I register a class so its dependencies get resolved?**

A: Register the class under `factories` in the module's `ConfigProvider`, using `Dot\DependencyInjection\Factory\AttributedServiceFactory::class`.

**Q: Can I inject a configuration value instead of a service?**

A: Yes, specify the configuration key path using dot notation, for example `config.example`.
