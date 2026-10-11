<?php

namespace Core;

class App {
    public function boot(): void {
        // 1. Load application configurations (Database, App Constants, etc.)
        $this->loadConfig();
        if (APP_SESSION === 'PHP_SESSION' && session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Load Route Definitions
        $this->loadRoutes();

        // 3. Capture Request & Dispatch
        $request = new Request();
        $response = Router::dispatch($request);

        // 4. Send Response back to the browser
        $this->sendResponse($response);
    }

    protected function loadConfig(): void {
        $config = BASE_PATH . '/config/config.php';

        if (file_exists($config)) {
            require_once $config;
        }
    }

    protected function loadRoutes(): void {
        $routes = BASE_PATH . '/app/routes/web.php';

        if (file_exists($routes)) {
            require_once $routes;
        }
    }

    protected function sendResponse(mixed $response): void {
        if (is_array($response) || is_object($response)) {
            header('Content-Type: application/json');
            echo json_encode($response);
        } else if (is_string($response)) {
            echo $response;
        }
    }
}
