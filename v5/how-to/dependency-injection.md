# Dependency Injection

Dependency injection is a design pattern used in software development to implement inversion of control.
In simpler terms, it's the act of providing dependencies for an object during instantiation.

In PHP, dependency injection can be implemented in various ways, including through constructor injection, setter injection and property injection.

Dotkernel Admin, through its [dot-dependency-injection](https://github.com/dotkernel/dot-dependency-injection) package focuses only on constructor injection.

## Usage

**Dotkernel Admin** comes out of the box with the [dot-dependency-injection](https://github.com/dotkernel/dot-dependency-injection) package, which provides all the functionality injecting dependencies into any object you want.

`dot-dependency-injection` determines the dependencies by looking at the `#[Inject]` attribute, added to the constructor of a class.
Each dependency is specified as a separate parameter of the `#[Inject]` attribute.

For our example we will inject `AdminService` and `config` dependencies into a `AdminController`.

```php
use Dot\DependencyInjection\Attribute\Inject;

class AdminController implements RequestHandlerInterface
{
    #[Inject(
        AdminService::class,
        "config",
    )]
    public function __construct(
        protected AdminServiceInterface $adminService,
        protected array $config,
    ) {
    }
}
```

> If your class needs the value of a specific configuration key, you can specify the path using dot notation: `config.example`

The next step is to register the class in the `ConfigProvider` under `factories` using
`Dot\DependencyInjection\Factory\AttributedServiceFactory::class`.

```php
public function getDependencies(): array
{
    return [
        'factories' => [
            AdminController::class => AttributedServiceFactory::class
        ]
    ];
}
```

That's it.
When your object is instantiated from the container, it will automatically have its dependencies resolved.

> Dependencies injection is available to any object within Dotkernel Admin.
> For example, you can inject dependencies in a service, a controller and so on, simply by registering them in the `ConfigProvider`.
