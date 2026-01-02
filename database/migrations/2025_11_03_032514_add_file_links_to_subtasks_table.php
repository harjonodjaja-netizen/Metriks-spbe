<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            if (! Schema::hasColumn('subtasks', 'file_links')) {
                $table->json('file_links')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            if (Schema::hasColumn('subtasks', 'file_links')) {
                $table->dropColumn('file_links');
            }
        });
    }
};
