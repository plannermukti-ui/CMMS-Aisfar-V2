<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            if (! Schema::hasColumn('equipments', 'pm_unit_model_id')) {
                $table->foreignUuid('pm_unit_model_id')->nullable()->after('reff_equip_id')->constrained('pm_unit_models')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipments', function (Blueprint $table) {
            if (Schema::hasColumn('equipments', 'pm_unit_model_id')) {
                $table->dropForeign(['pm_unit_model_id']);
                $table->dropColumn('pm_unit_model_id');
            }
        });
    }
};
