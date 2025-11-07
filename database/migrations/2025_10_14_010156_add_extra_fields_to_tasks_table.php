<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'progress')) {
                $table->integer('progress')->default(0)->after('status');
            }
            if (!Schema::hasColumn('tasks', 'file_links')) {
                $table->text('file_links')->nullable()->after('progress');
            }
            if (!Schema::hasColumn('tasks', 'notes')) {
                $table->text('notes')->nullable()->after('file_links');
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['progress', 'file_links', 'notes']);
        });
    }
};