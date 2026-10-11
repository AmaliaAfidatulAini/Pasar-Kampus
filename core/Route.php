<?php

namespace Core;

class Route {
    public string $method;
    public string $uri;
	
    /** @var callable|array */
    public $action;
    public array $middlewares = [];

    public function __construct(string $method, string $uri, callable|array $action) {
        $this->method = strtoupper($method);
        $this->uri = '/' . trim($uri, '/');
        $this->action = $action;
    }

    public function middleware(string|array|callable $middleware): self {
        if (is_array($middleware)) {
            $this->middlewares = array_merge($this->middlewares, $middleware);
        } else {
            $this->middlewares[] = $middleware;
        }
        return $this;
    }
}
