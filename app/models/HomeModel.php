<?php

namespace App\Models;

use Core\Model;

class HomeModel extends Model
{
    protected string $table_name = 'pelanggan';

    public function message(): string
    {
        // $data = $this->query("SELECT * FROM {$this->table_name}")->fetchAll();
        return 'Hello World!';
    }
}
