<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            // Add columns if they don't exist
            if (!Schema::hasColumn('subtasks', 'priority')) {
                $table->string('priority')->default('Low')->after('description');
            }
            if (!Schema::hasColumn('subtasks', 'status')) {
                $table->string('status')->default('Not Started')->after('priority');
            }
            if (!Schema::hasColumn('subtasks', 'assigned_to')) {
                $table->string('assigned_to')->nullable()->after('status');
            }
            if (!Schema::hasColumn('subtasks', 'start_date')) {
                $table->date('start_date')->nullable()->after('assigned_to');
            }
            if (!Schema::hasColumn('subtasks', 'due_date')) {
                $table->date('due_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('subtasks', 'notes')) {
                $table->text('notes')->nullable()->after('due_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropColumn(['priority', 'status', 'assigned_to', 'start_date', 'due_date', 'notes']);
        });
    }
};
