<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationTablesExistTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('tasks'));
        $this->assertTrue(Schema::hasTable('subtasks'));

        // File link tables
        $this->assertTrue(Schema::hasTable('task_file_links'));
        $this->assertTrue(Schema::hasTable('subtask_file_links'));
    }
}
