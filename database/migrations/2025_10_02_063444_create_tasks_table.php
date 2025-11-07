<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_name');
            $table->text('description');
            $table->string('priority');
            $table->string('status');
            $table->string('assigned_to');
            $table->date('start_date');
            $table->date('due_date');
            $table->text('file_links')->nullable();
            $table->text('notes')->nullable();
            $table->integer('progress')->default(0); // ✅ ONLY ONCE, with default value
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
