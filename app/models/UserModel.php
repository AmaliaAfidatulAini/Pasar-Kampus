<?php

namespace App\Models;

use Core\Model;

class UserModel extends Model
{
    protected string $table_name = 'users';

    public function findByEmail(string $email): ?array
    {
        $user = $this->query(
            "SELECT id, name, email, password FROM {$this->table_name} WHERE email = :email",
            ['email' => $email]
        )->fetch();

        return $user ?: null;
    }

    public function find(int $id): ?array
    {
        $user = $this->query(
            "SELECT id, name, email, password, photo_profile, created_at FROM {$this->table_name} WHERE id = :id",
            ['id' => $id]
        )->fetch();
        return $user ?: null;
    }

    public function create(string $name, string $email, string $password): int
    {
        $statement = $this->query(
            "INSERT INTO {$this->table_name} (name, email, password)
             VALUES (:name, :email, :password) RETURNING id",
            ['name' => $name, 'email' => $email, 'password' => $password]
        );

        return (int) $statement->fetchColumn();
    }

    public function update(int $id, string $name, string $email, ?string $photoProfile = null): bool
    {
        $photoSql = $photoProfile === null ? '' : ', photo_profile = :photo_profile';
        $params = ['id' => $id, 'name' => $name, 'email' => $email];
        if ($photoProfile !== null) $params['photo_profile'] = $photoProfile;
        return $this->query(
            "UPDATE {$this->table_name} SET name = :name, email = :email{$photoSql}, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            $params
        )->rowCount() > 0;
    }

    public function updatePassword(int $id, string $password): bool
    {
        return $this->query(
            "UPDATE {$this->table_name} SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id",
            ['id' => $id, 'password' => $password]
        )->rowCount() > 0;
    }
}
