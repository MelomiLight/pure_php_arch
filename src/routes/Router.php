<?php

namespace routes;

use app\Contracts\Request;
use helpers\HttpHelpers;
use ReflectionException;
use ReflectionMethod;

class Router
{
    protected static array $routes = [];
    protected static array $middleware = [];

    public static function get($uri, array $action, array $middleware = []): void
    {
        self::$routes[$uri]['methods']['GET'] = compact('action', 'middleware');
    }

    public static function post($uri, array $action, array $middleware = []): void
    {
        self::$routes[$uri]['methods']['POST'] = compact('action', 'middleware');
    }

    public static function put($uri, array $action, array $middleware = []): void
    {
        self::$routes[$uri]['methods']['PUT'] = compact('action', 'middleware');
    }

    public static function patch($uri, array $action, array $middleware = []): void
    {
        self::$routes[$uri]['methods']['PATCH'] = compact('action', 'middleware');
    }

    public static function delete($uri, array $action, array $middleware = []): void
    {
        self::$routes[$uri]['methods']['DELETE'] = compact('action', 'middleware');
    }

    /**
     * @throws ReflectionException
     */
    public static function dispatch($uri, $method): void
    {
        if (!isset(self::$routes[$uri])) {
            header('Location: /');
            exit;
        }

        if (!isset(self::$routes[$uri]['methods'][$method])) {
            echo "405 Method Not Allowed";
            exit;
        }

        $route = self::$routes[$uri]['methods'][$method];
        $action = $route['action'];
        $middleware = $route['middleware'];
        $request = new Request();

        if (empty($middleware)) {
            self::invokeAction($action, $request);
        } else {
            foreach ($middleware as $middlewareClass) {
                $middlewareInstance = new $middlewareClass();
                $middlewareInstance->handle($request, function ($request) use ($action) {
                    self::invokeAction($action, $request);
                });
            }
        }
    }

    private static function invokeAction($action, $request): void
    {
        try {
            $controller = new $action[0]();
            $reflection = new ReflectionMethod($action[0], $action[1]);
            $parameters = $reflection->getParameters();

            $args = [];

            foreach ($parameters as $parameter) {
                $type = $parameter->getType();
                if ($type && !$type->isBuiltin()) {
                    $className = $type->getName();
                    $args[] = new $className();
                } else {
                    $args[] = $request;
                }
            }

            call_user_func_array([$controller, $action[1]], $args);
        } catch (ReflectionException $e) {
            HttpHelpers::responseJson(['error' => $e->getMessage()], 500);
        }
    }
}