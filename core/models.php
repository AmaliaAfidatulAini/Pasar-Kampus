<?php

function require_model(string|array ...$models): void
{
    foreach ($models as $names) {
        foreach ((array) $names as $name) {
            if (!is_string($name) || preg_match('/\A[a-zA-Z0-9_-]+(?:\/[a-zA-Z0-9_-]+)*\z/', $name) !== 1) {
                throw new InvalidArgumentException('Invalid model name.');
            }

            $path = BASE_PATH . '/models/' . $name . '.php';
            if (!is_file($path)) {
                throw new RuntimeException('Model not found: ' . $name);
            }

            require_once $path;
        }
    }
}
