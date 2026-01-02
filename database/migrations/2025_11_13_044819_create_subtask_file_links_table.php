<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - STEP 1: Create the new subtask_file_links table
     */
    public function up(): void
    {
        if (Schema::hasTable('subtask_file_links')) {
            return;
        }

        // Create subtask_file_links table (just like TaskFileLink table)
        Schema::create('subtask_file_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtask_id')
                  ->constrained('subtasks')
                  ->onDelete('cascade');
            $table->string('link_name');
            $table->text('link_url');
            $table->text('description')->nullable();
            $table->timestamps();

            // Index for fast lookups
            $table->index('subtask_id');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('subtask_file_links');
    }
};