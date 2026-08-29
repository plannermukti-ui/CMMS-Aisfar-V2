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
        if (Schema::hasTable('parts') && ! Schema::hasColumn('parts', 'vendor_id')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->foreignUuid('vendor_id')->nullable()->after('name')->constrained('vendors')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parts') && Schema::hasColumn('parts', 'vendor_id')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            });
        }
    }
};
