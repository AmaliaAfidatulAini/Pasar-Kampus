<?php

namespace Core;

class Request {
    protected $params = [];

    public function getMethod(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function getUri(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        return '/' . trim($uri, '/');
    }

    public function setParams(array $params): void {
        $this->params = $params;
    }

    public function param(string $key, $default = null) {
        return $this->params[$key] ?? $default;
    }

    public function body($key = null, $default = null) {
        $data = array_merge($_GET, $_POST);
        
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $json = json_decode($input, true);
            if (is_array($json)) {
                $data = array_merge($data, $json);
            }
        }

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }

    /**
     * Return uploaded files in PHP's standard upload shape.
     * Multi-file fields are normalized to a list of file records.
     */
    public function file(?string $key = null): mixed {
        if ($key === null) {
            return $_FILES;
        }

        if (!array_key_exists($key, $_FILES)) {
            return null;
        }

        $file = $_FILES[$key];
        if (!is_array($file['name'] ?? null)) {
            return $file;
        }

        $files = [];
        foreach (array_keys($file['name']) as $index) {
            $files[] = [
                'name' => $file['name'][$index] ?? null,
                'type' => $file['type'][$index] ?? null,
                'tmp_name' => $file['tmp_name'][$index] ?? null,
                'error' => $file['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $file['size'][$index] ?? 0,
            ];
        }

        return $files;
    }
}
