<?php

namespace Core;

class Router {
    protected static array $routes = [];
    protected static array $groupPrefixes = [];
    protected static array $groupMiddlewares = [];

    public static function get(string $uri, callable|array $action): Route {
        return self::addRoute('GET', $uri, $action);
    }

    public static function post(string $uri, callable|array $action): Route {
        return self::addRoute('POST', $uri, $action);
    }

    public static function put(string $uri, callable|array $action): Route {
        return self::addRoute('PUT', $uri, $action);
    }

    public static function delete(string $uri, callable|array $action): Route {
        return self::addRoute('DELETE', $uri, $action);
    }

    /**
     * Group routes with a common prefix and optional middleware(s).
     *
     * @param string $prefix URI prefix (can be empty string '')
     * @param callable $callback Callback containing route definitions
     * @param string|array|callable $middleware Middleware(s) to apply to the group
     */
    public static function group(string $prefix, callable $callback, string|array|callable $middleware = []): void {
        self::$groupPrefixes[] = $prefix;

        $normalizedMiddleware = is_array($middleware) ? $middleware : ($middleware ? [$middleware] : []);
        self::$groupMiddlewares[] = $normalizedMiddleware;

        try {
            call_user_func($callback);
        } finally {
            array_pop(self::$groupPrefixes);
            array_pop(self::$groupMiddlewares);
        }
    }

    protected static function addRoute(string $method, string $uri, callable|array $action): Route {
        $fullUri = self::prefixUri($uri);
        $route = new Route($method, $fullUri, $action);

        foreach (self::$groupMiddlewares as $middlewares) {
            if (!empty($middlewares)) {
                $route->middleware($middlewares);
            }
        }

        self::$routes[] = $route;
        return $route;
    }

    protected static function prefixUri(string $uri): string {
        $segments = [];

        foreach (self::$groupPrefixes as $prefix) {
            $trimmed = trim($prefix, '/');
            if ($trimmed !== '') {
                $segments[] = $trimmed;
            }
        }

        $trimmedUri = trim($uri, '/');
        if ($trimmedUri !== '') {
            $segments[] = $trimmedUri;
        }

        return '/' . implode('/', $segments);
    }

    public static function getRoutes(): array {
        return self::$routes;
    }

    public static function clearRoutes(): void {
        self::$routes = [];
        self::$groupPrefixes = [];
        self::$groupMiddlewares = [];
    }

    /**
     * Match current request against registered routes and execute middleware chain.
     */
    public static function dispatch(Request $request) {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach (self::$routes as $route) {
            if ($route->method !== $method) {
                continue;
            }

            // Convert route pattern `/user/{id}` to regex `^/user/(?P<id>[^/]+)$`
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route->uri);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                // Extract named parameter pairs
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                // Build the execution pipeline (Middleware -> Final Action)
                return self::runPipeline($route, $request);
            }
        }

        return redirect("404", 400);
    }

    protected static function runPipeline(Route $route, Request $request) {
        $pipeline = array_map(function ($middleware) {
            if (is_string($middleware) && class_exists($middleware)) {
                return [new $middleware(), 'handle'];
            }
            if (is_array($middleware)) {
                $class = is_string($middleware[0]) ? new $middleware[0]() : $middleware[0];
                $method = $middleware[1] ?? 'handle';
                return [$class, $method];
            }
            return $middleware;
        }, $route->middlewares);

        // Core action callback to be executed at the end of the chain
        $coreAction = function ($req) use ($route) {
            if (is_callable($route->action)) {
                return call_user_func($route->action, $req);
            }

            // Controller@method syntax handling (e.g. [HomeController::class, 'index'])
            if (is_array($route->action)) {
                [$controllerClass, $method] = $route->action;
                $controller = is_string($controllerClass) ? new $controllerClass() : $controllerClass;
                return call_user_func([$controller, $method], $req);
            }
        };

        // Run onion-style middleware pipeline
        $runner = array_reduce(
            array_reverse($pipeline),
            function ($next, $middleware) {
                return function ($req) use ($next, $middleware) {
                    if (is_array($middleware)) {
                        [$instance, $method] = $middleware;
                        return call_user_func([$instance, $method], $req, $next);
                    }
                    if (is_callable($middleware)) {
                        return $middleware($req, $next);
                    }
                    if (is_object($middleware) && method_exists($middleware, 'handle')) {
                        return $middleware->handle($req, $next);
                    }
                };
            },
            $coreAction
        );

        return $runner($request);
    }
}
