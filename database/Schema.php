<?php

use Core\Migration;

class Schema extends Migration
{
    /**
     * Query execution order list for $this->all().
     * If not provided or empty, filesystem directory order will be used.
     * 
     * @var array<string>
     */
    protected array $order = ['User', 'Post'];

    /**
     * Execute table schema creation queries.
     */
    public function create(): void
    {
        $this->all()->create();
    }

    /**
     * Execute database seed queries.
     */
    public function seed(): void
    {
        $this->all()->seed();
    }

    /**
     * Execute drop / destroy schema queries.
     */
    public function drop(): void
    {
        $this->all()->drop();
    }
}