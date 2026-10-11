<?php

namespace Core;

use Exception;

class Migration
{
    protected Database $db;
    protected array $order = [];
    protected string $baseDir;
    protected array $selectedTables = [];

    public function __construct(?Database $db = null, ?string $baseDir = null)
    {
        $this->db = $db ?? new Database();
        $this->baseDir = $baseDir ?? dirname(__DIR__) . '/database';
    }

    public function setBaseDir(string $dir): static
    {
        $this->baseDir = $dir;
        return $this;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * Get target tables to run (from $order or filesystem scanning).
     */
    public function getTargetTables(): array
    {
        if (!empty($this->order)) {
            return $this->order;
        }
        return $this->getTablesFromFilesystem();
    }

    /**
     * Specify target table(s) for migration actions.
     */
    public function table(string|array $tables): MigrationRunner
    {
        $selected = is_array($tables) ? $tables : [$tables];
        return new MigrationRunner($this->db, $this->baseDir, $selected);
    }

    /**
     * Target all tables defined in $order or scanned from filesystem.
     */
    public function all(): MigrationRunner
    {
        return new MigrationRunner($this->db, $this->baseDir, $this->getTargetTables());
    }

    /**
     * Scan database directory for folders containing migration SQL files.
     */
    public function getTablesFromFilesystem(): array
    {
        if (!is_dir($this->baseDir)) {
            return [];
        }

        $tables = [];
        $items = scandir($this->baseDir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $fullPath = $this->baseDir . '/' . $item;
            if (is_dir($fullPath)) {
                $tables[] = $item;
            }
        }
        return $tables;
    }

    /**
     * Create tables / execute schemas for all target tables.
     */
    public function create(): void
    {
        $tables = !empty($this->selectedTables) ? $this->selectedTables : $this->getTargetTables();
        foreach ($tables as $table) {
            $this->executeFileForTable($table, 'schema.sql');
        }
    }

    /**
     * Run seeders for all target tables.
     */
    public function seed(): void
    {
        $tables = !empty($this->selectedTables) ? $this->selectedTables : $this->getTargetTables();
        foreach ($tables as $table) {
            $this->executeFileForTable($table, 'seeds.sql');
        }
    }

    /**
     * Drop tables / destroy schemas for all target tables.
     */
    public function drop(): void
    {
        $tables = !empty($this->selectedTables) ? $this->selectedTables : $this->getTargetTables();
        foreach ($tables as $table) {
            $this->executeFileForTable($table, 'destroy.sql');
        }
    }

    /**
     * Execute raw SQL statement.
     */
    public function executeRaw(string $sql, array $bindings = []): bool
    {
        $sql = trim($sql);
        if (empty($sql)) {
            return true;
        }
        return $this->db->statement($sql, $bindings);
    }

    /**
     * Execute SQL file for a specific table directory.
     */
    public function executeFileForTable(string $table, string $filename): void
    {
        $filePath = $this->baseDir . '/' . $table . '/' . $filename;
        if (file_exists($filePath)) {
            $this->executeSqlFile($filePath);
        }
    }

    /**
     * Execute an object's SQL files based on action (create, seed, drop).
     */
    public function executeObject(string $objectName, string $action = 'create'): void
    {
        $dir = $this->baseDir . '/' . $objectName;
        if (!is_dir($dir)) {
            throw new Exception("Database directory not found for object '{$objectName}': {$dir}");
        }

        $defaultFiles = [
            'create' => 'schema.sql',
            'seed' => 'seeds.sql',
            'drop' => 'destroy.sql',
        ];

        $targetFile = $dir . '/' . ($defaultFiles[$action] ?? 'schema.sql');

        if (file_exists($targetFile)) {
            $this->executeSqlFile($targetFile);
            return;
        }

        $files = glob($dir . '/*.sql');
        if (empty($files)) {
            return;
        }

        sort($files);
        foreach ($files as $file) {
            $basename = basename($file);
            if ($action === 'create' && in_array($basename, ['seeds.sql', 'destroy.sql'])) {
                continue;
            }
            if ($action === 'seed' && !str_contains(strtolower($basename), 'seed')) {
                continue;
            }
            if ($action === 'drop' && !str_contains(strtolower($basename), 'destroy') && !str_contains(strtolower($basename), 'drop')) {
                continue;
            }
            $this->executeSqlFile($file);
        }
    }

    /**
     * Read and execute SQL file using database connection.
     */
    public function executeSqlFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $sql = file_get_contents($filePath);
        $sql = trim($sql);

        if (empty($sql)) {
            return;
        }

        $this->db->connection()->exec($sql);
    }

    /**
     * Resolve Schema instance from database/Schema.php or return base Migration.
     */
    public static function resolveSchema(?string $baseDir = null): static
    {
        $baseDir ??= dirname(__DIR__) . '/database';
        $schemaFile = $baseDir . '/Schema.php';

        if (file_exists($schemaFile)) {
            require_once $schemaFile;
            if (class_exists('Schema')) {
                return new \Schema(null, $baseDir);
            }
        }

        return new static(null, $baseDir);
    }
}

/**
 * Helper class to execute table actions without triggering infinite recursion on Schema subclasses.
 */
class MigrationRunner
{
    protected Database $db;
    protected string $baseDir;
    protected array $tables;

    public function __construct(Database $db, string $baseDir, array $tables)
    {
        $this->db = $db;
        $this->baseDir = $baseDir;
        $this->tables = $tables;
    }

    public function create(): void
    {
        foreach ($this->tables as $table) {
            $this->executeFile($table, 'schema.sql');
        }
    }

    public function seed(): void
    {
        foreach ($this->tables as $table) {
            $this->executeFile($table, 'seeds.sql');
        }
    }

    public function drop(): void
    {
        foreach ($this->tables as $table) {
            $this->executeFile($table, 'destroy.sql');
        }
    }

    protected function executeFile(string $table, string $filename): void
    {
        $filePath = $this->baseDir . '/' . $table . '/' . $filename;
        if (file_exists($filePath)) {
            $sql = trim(file_get_contents($filePath));
            if (!empty($sql)) {
                $this->db->connection()->exec($sql);
            }
        }
    }
}
