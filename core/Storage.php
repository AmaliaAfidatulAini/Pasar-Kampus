<?php

namespace Core;

final class Storage
{
    private function __construct()
    {
    }

    /**
     * Store one uploaded file and return its path relative to public/uploads.
     */
    public static function put(array $file, string $directory = '', bool $relative = true): string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException(
                'File upload failed with error code ' . ($file['error'] ?? 'unknown') . '.'
            );
        }

        $temporaryPath = $file['tmp_name'] ?? null;

        if (!is_string($temporaryPath) || !is_uploaded_file($temporaryPath)) {
            throw new \RuntimeException('Invalid uploaded file.');
        }

        $mime = mime_content_type($temporaryPath) ?: '';
        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        if (!isset($extensions[$mime])) {
            throw new \RuntimeException('Only JPG, PNG, GIF, and WebP images are allowed.');
        }

        $directory = self::cleanRelativePath($directory);
        $uploadDirectory = BASE_PATH . '/public/uploads';
        $targetDirectory = $uploadDirectory . ($directory !== '' ? '/' . $directory : '');

        if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true)) {
            throw new \RuntimeException('Unable to create upload directory.');
        }

        $extension = $extensions[$mime];
        $filename = bin2hex(random_bytes(16)) . ($extension !== '' ? '.' . $extension : '');
        $relativePath = ($directory !== '' ? $directory . '/' : '') . $filename;
        $targetPath = $uploadDirectory . '/' . $relativePath;

        if (!move_uploaded_file($temporaryPath, $targetPath)) {
            throw new \RuntimeException('Unable to move uploaded file.');
        }

        return $relative ? $relativePath : $filename;
    }

    public static function delete(string $path): bool
    {
        $path = self::cleanRelativePath($path);
        $file = BASE_PATH . '/public/uploads/' . $path;

        return is_file($file) && unlink($file);
    }

    public static function check(string $path): bool
    {
        $path = self::cleanRelativePath($path);
        return is_file(BASE_PATH . '/public/uploads/' . $path);
    }

    private static function cleanRelativePath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path), '/');

        if ($path === '' || str_contains($path, "\0") || preg_match('#(^|/)\.\.(/|$)#', $path)) {
            if ($path === '') {
                return '';
            }

            throw new \InvalidArgumentException('Invalid upload path.');
        }

        return $path;
    }
}
