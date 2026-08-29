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
        if (Schema::hasTable('stock_opname_items') && ! Schema::hasColumn('stock_opname_items', 'rack_location')) {
            Schema::table('stock_opname_items', function (Blueprint $table) {
                $table->string('rack_location')->nullable()->after('uom');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stock_opname_items') && Schema::hasColumn('stock_opname_items', 'rack_location')) {
            Schema::table('stock_opname_items', function (Blueprint $table) {
                $table->dropColumn('rack_location');
            });
        }
    }
};
