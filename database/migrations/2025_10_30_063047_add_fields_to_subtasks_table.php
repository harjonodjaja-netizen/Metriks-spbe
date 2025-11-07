<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->string('status')->default('Not Started')->after('description');
            $table->string('assigned_to')->nullable()->after('status');
            $table->date('start_date')->nullable()->after('assigned_to');
            $table->date('due_date')->nullable()->after('start_date');
            $table->text('notes')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropColumn(['status', 'assigned_to', 'start_date', 'due_date', 'notes']);
        });
    }
};
