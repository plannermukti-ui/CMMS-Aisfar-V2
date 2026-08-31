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
        Schema::table('pm_unit_models', function (Blueprint $table) {
            $table->foreignUuid('reff_equip_id')->nullable()->after('name')->constrained('reff_equips')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pm_unit_models', function (Blueprint $table) {
            $table->dropForeign(['reff_equip_id']);
            $table->dropColumn('reff_equip_id');
        });
    }
};
