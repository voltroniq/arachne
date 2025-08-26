# Routing & Middleware

Arachne uses FastRoute for routing and a PSR-15 style middleware pipeline (Relay) to handle requests.

## Defining routes

Routes live in `config/routes.php`. Example:

```php
use FastRoute\RouteCollector;
use Arachne\Controllers\HomeController;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/async', [HomeController::class, 'asyncExample']);
};
```

## FastRouteMiddleware

- Dispatches the incoming request against the route table
- Resolves controllers from the DI container by class name
- Calls controller methods and passes an optional `?Scheduler` as the second argument

### Controller signature expectations

If your middleware passes a Scheduler, controller methods should accept it as an optional argument:

```php
public function index(ServerRequestInterface $request, ?\Arachne\Async\Scheduler $scheduler = null)
```

If you prefer route params, you can add a third argument for `$params`.