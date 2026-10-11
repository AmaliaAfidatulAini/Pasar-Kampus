<?php

namespace App\Models;

use Core\Model;

class PostModel extends Model
{
    protected string $table_name = 'posts';

    public function all(): array
    {
        return $this->query(
            "SELECT posts.*, users.name AS author_name
            FROM {$this->table_name} JOIN users ON users.id = posts.user_id
            ORDER BY posts.created_at DESC"
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->query("SELECT * FROM {$this->table_name} WHERE id = :id", ['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function photos(int $id): array
    {
        $photos = json_decode($this->find($id)['photos'] ?? '[]', true);
        return is_array($photos) ? $photos : [];
    }

    public function create(int $userId, string $title, string $content, ?string $photos): void
    {
        $this->query(
            "INSERT INTO {$this->table_name} (user_id, title, content, photos)
             VALUES (:user_id, :title, :content, :photos)",
            ['user_id' => $userId, 'title' => $title, 'content' => $content, 'photos' => $photos]
        );
    }

    public function update(int $id, int $userId, string $title, string $content, ?string $photos): bool
    {
        return $this->query(
            "UPDATE {$this->table_name}
             SET title = :title, content = :content, photos = :photos, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId, 'title' => $title, 'content' => $content, 'photos' => $photos]
        )->rowCount() > 0;
    }

    public function delete(int $id, int $userId): bool
    {
        return $this->query(
            "DELETE FROM {$this->table_name} WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId]
        )->rowCount() > 0;
    }
}
