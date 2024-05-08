# Access Manager

The `francerz\access-manager` library provides a PSR-15 middleware class that
manages access control to routes based on granted permissions.

## Installation

You can install the library via Composer. Run the following command:

```sh
composer require francerz/access-manager
```

## Usage

### Using Slim Framework

1. Create an `UserGrantsProviderInterface` implementation to retrieve current
   user grants.

   ```php
   use Francerz\AccessManager\UserGrantsProviderInterface;

   class CurrentUserGrantsProvider implements UserGrantsProviderInterface
   {
       public function getUserGrants(): string
       {
           // Returns the current user grants.
           return $_SESSION['user_grants'];
       }
   }
   ```

2. Configure the access middleware in your Slim\App routing:

   ```php
   use Francerz\AccessManager\AccessMiddleware;
   use Slim\Routing\RouteCollectorProxy;
   
   $app = new \Slim\App();
   
   // A PSR-17 ResponseFactory implemenation
   $responseFactory = new \GuzzleHttp\Psr7\HttpFactory();
   
   $userPermissionProvider = new CurrentUserGrantsProvider();
   $accessMiddleware = new AccessMiddleware($userPermissionProvider, $responseFactory);
   
   $app->get('[/]', [HomeController::class, 'indexGet'])
       ->addMiddleware($accessMiddleware->allow('user'));
   
   $app->group('/admin', function(RouteCollectorProxy $route) {
       // Restricted admin routes.
       $route->get('[/]', [AdminController::class, 'indexGet']);
   })->addMiddleware($accessMiddleware->allow('admin'));
   ```

## Permission Syntax

The `allow` method accept a permission string with a syntax similar to boolean
logic:

- Use space to separate individual permissions; each space acts as an `AND`
  operator.
- Use character `|` to represent an `OR` operator, e.g., `'read | write'`.

## License

This library is licensed under the MIT License. see the [LICENSE](./LICENSE)
file for details.
