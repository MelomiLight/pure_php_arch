# PURE PHP ARCHITECTURE, LARAVEL COPY
AUTOLOAD, NGINX, DOCKER-COMPOSE, SELF-WRITTED MIGRATIONS, JWT
## Public/index.php - entry point
```PHP
<?php

use bootstrap\App;

define("APP_PATH", dirname(__DIR__));

require_once APP_PATH . '/vendor/autoload.php';

$app = new App();
$app->start();
```
## src/routes/routes.php
```PHP
<?php

// Define routes
use app\Http\Controllers\Auth\AuthController;
use app\Http\Controllers\HomeController;
use app\Http\Middleware\JWTMiddleware;
use routes\Router;

Router::post('/register', [AuthController::class, 'register']);
Router::post('/login', [AuthController::class, 'login']);

Router::get('/register', [AuthController::class, 'showRegisterForm']);
Router::get('/login', [AuthController::class, 'showLoginForm']);

//protected
Router::get('/', [HomeController::class, 'index'], [JWTMiddleware::class]);
```

## src/app/Models/User.php - Custom MODEL classes
```PHP
<?php

namespace app\Models;

use app\Traits\Crudable;
use app\Traits\Tokenable;

class User
{
    use Crudable, Tokenable;

    private static string $table_name = 'users';
    private static array $fields = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
    ];


}
```
## src/app/Traits/Crudable.php - Traits for models, allows implement crud mechanic for any model
``` PHP
trait Crudable
public static function create(array $data)
public static function find(int $id, int $type = PDO::FETCH_OBJ)
public static function where(string $column_name, $value)
```

## src/app/Http/Controllers/Auth/AuthController.php, Reflections used for arguments DI(dependency injection) - src/routes/Router.php
```PHP
    public function register(UserStoreRequest $request): void
    {
        $data = $request->validated();

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        $data = User::create($data);

        if (!$data) {
            HttpHelpers::responseJson(['message' => 'Failed to create user'], 500);
        }

        $userResource = UserResource::make($data);

        HttpHelpers::responseJson([
            'message' => 'User created successfully',
            'data' => $userResource,
        ], 201);
    }
```
## src/app/Http/Requests/UserStoreRequest.php - Custom requests like in Laravel
```PHP
<?php

namespace app\Http\Requests;

use app\Contracts\Request;

class UserStoreRequest extends Request
{
    public function rules()
    {
        return [
            'username' => 'required | between:1,255 | unique: users,username',
            'first_name' => 'between:1,255',
            'last_name' => 'between:1,255',
            'email' => 'required | email | unique: users,email',
            'password' => 'required | secure',
        ];
    }

}
```
## src/app/Http/Resources/UserResource.php - Custom Resources
```PHP
<?php

namespace app\Http\Resources;

class UserResource
{
    public static function make($resource): array
    {
        return [
            'id' => $resource->id,
            'email' => $resource->email,
            'username' => $resource->username,
            'first_name' => $resource->first_name,
            'last_name' => $resource->last_name,
        ];
    }
}
```
## src/bootstrap/MigrationManager.php - Custom Migrations
```PHP
public function runMigrations()
public function rollbackMigrations()
public function createMigration()
```
## src/app/Http/Middleware/JWTMiddleware.php - Custom JWT Check Middleware
```PHP
<?php

namespace app\Http\Middleware;

use app\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use helpers\HttpHelpers;

class JWTMiddleware implements MiddlewareInterface
{
    public function handle($request, $next)
    {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            HttpHelpers::responseJson(['error' => 'Unauthorized'], 401);
        }

        try {
            $header = trim($_SERVER['HTTP_AUTHORIZATION']);
            $token = $this->getBearerToken($header);
            $decoded = JWT::decode($token, new Key($_ENV['APP_KEY'], 'HS256'));
            $user = User::find($decoded->user_id);
            $_SERVER['user'] = $user;
        } catch (\Exception $e) {
            HttpHelpers::responseJson(['error' => $e->getMessage()], 400);
        }

        return $next($request);
    }

    private function getBearerToken($header)
    {
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
```
