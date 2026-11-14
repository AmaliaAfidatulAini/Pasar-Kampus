<?php

class Component
{
	public static function include(string $name, ?array $data = null): void
	{
        if (preg_match('~^[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*(?:\.php)?$~', $name) !== 1) {
			throw new InvalidArgumentException('Invalid component name.');
		}

		$filename = str_ends_with($name, '.php') ? $name : $name . '.php';
		$path = (defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__)) . '/components/' . $filename;
		if (!is_file($path)) {
			throw new RuntimeException('Component not found: ' . $name);
		}

		extract($data ?? [], EXTR_SKIP);
		require $path;
	}

	public static function flush(): void
    {
        ob_start();
    }

    public static function endFlush(): string
    {
        return ob_get_clean() ?: '';
    }
}