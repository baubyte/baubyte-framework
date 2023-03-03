<?php

namespace Baubyte\Routing;

use Baubyte\Container\DependencyInjection;
use Closure;
use Baubyte\Http\Request;
use Baubyte\Http\HttpMethod;
use Baubyte\Http\HttpNotFoundException;
use Baubyte\Http\Response;

/**
 * HTTP router.
 */
class Router {
    /**
     * HTTP routes.
     *
     * @var array<string, Route[]>
     */
    protected array $routes = [];

    /**
     * Create a new router.
     */
    public function __construct() {
        foreach (HttpMethod::cases() as $method) {
            $this->routes[$method->value()] = [];
        }
    }

    /**
     * Resolve the route of the `$request`.
     *
     * @param Request $request
     * @return Route
     * @throws HttpNotFoundException when route is not found
     */
    public function resolveRoute(Request $request): Route {
        $method = $request->method()->value();
        if (!is_null($request->data("_method")) && HttpMethod::isValid($request->data("_method"))) {
            $method = $request->data("_method");
        }
        foreach ($this->routes[$method] as $route) {
            if ($route->matches($request->uri())) {
                return $route;
            }
        }
        throw new HttpNotFoundException();
    }
    /**
     * Resolves the requested route.
     *
     * @param \Baubyte\Http\Request $request
     * @return \Baubyte\Http\Response
     */
    public function resolve(Request $request): Response {
        $route = $this->resolveRoute($request);
        $request->setRoute($route);
        $action = $route->action();

        $middlewares = $route->middlewares();

        if (is_array($action)) {
            $controller = new $action[0]();
            $action[0] = $controller;
            foreach ($controller->middlewares() as $middleware) {
                if (array_search($middleware, $middlewares) === false) {
                    array_push($middlewares, $middleware);
                }
            }
        }

        $params = DependencyInjection::resolveParameters($action, $request->routeParameters());

        return $this->runMiddlewares(
            $request,
            $middlewares,
            fn () => call_user_func($action, ...$params)
        );
    }

    /**
     * Run middleware stack and return final response.
     *
     * @param \Baubyte\Http\Request $request
     * @param \Baubyte\Http\Middleware[] $middlewares
     * @param callable $target
     * @return \Baubyte\Http\Response
     */
    protected function runMiddlewares(Request $request, array $middlewares, callable $target): Response {
        if (count($middlewares) === 0) {
            return $target();
        }
        return $middlewares[0]->handle(
            $request,
            fn ($request) => $this->runMiddlewares(
                $request,
                array_slice($middlewares, 1),
                $target
            )
        );
    }
    /**
     * Register a new route with the given `$method` and `$uri`.
     *
     * @param HttpMethod $method
     * @param string $uri
     * @param Closure|array $action
     * @return Route
     */
    protected function registerRoute(HttpMethod $method, string $uri, Closure|array $action): Route {
        $route = new Route($uri, $action);
        return $this->routes[$method->value()][] = $route;
    }

    /**
     * Register a GET route with the given `$uri` and `$action`.
     *
     * @param string $uri
     * @param Closure|array $action
     * @return Route
     */
    public function get(string $uri, Closure|array $action): Route {
        return $this->registerRoute(HttpMethod::GET(), $uri, $action);
    }

    /**
     * Register a POST route with the given `$uri` and `$action`.
     *
     * @param string $uri
     * @param Closure|array $action
     * @return Route
     */
    public function post(string $uri, Closure|array $action): Route {
        return $this->registerRoute(HttpMethod::POST(), $uri, $action);
    }

    /**
     * Register a PUT route with the given `$uri` and `$action`.
     *
     * @param string $uri
     * @param Closure|array $action
     * @return Route
     */
    public function put(string $uri, Closure|array $action): Route {
        return $this->registerRoute(HttpMethod::PUT(), $uri, $action);
    }

    /**
     * Register a PATCH route with the given `$uri` and `$action`.
     *
     * @param string $uri
     * @param Closure|array $action
     * @return Route
     */
    public function patch(string $uri, Closure|array $action): Route {
        return $this->registerRoute(HttpMethod::PATCH(), $uri, $action);
    }

    /**
     * Register a DELETE route with the given `$uri` and `$action`.
     *
     * @param string $uri
     * @param Closure|array $action
     * @return Route
     */
    public function delete(string $uri, Closure|array $action): Route {
        return $this->registerRoute(HttpMethod::DELETE(), $uri, $action);
    }
}
