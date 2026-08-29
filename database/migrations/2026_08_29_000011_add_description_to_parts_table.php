<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('parts') && ! Schema::hasColumn('parts', 'description')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parts') && Schema::hasColumn('parts', 'description')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
