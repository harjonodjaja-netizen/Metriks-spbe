<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->string('subtask_name');
            $table->text('description')->nullable();
            $table->string('priority')->default('Low'); // Added
            $table->string('status')->default('Not Started'); // Added
            $table->string('assigned_to')->nullable(); // Added
            $table->date('start_date')->nullable(); // Added
            $table->date('due_date')->nullable(); // Added
            $table->text('notes')->nullable(); // Added
            $table->boolean('completed')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};
