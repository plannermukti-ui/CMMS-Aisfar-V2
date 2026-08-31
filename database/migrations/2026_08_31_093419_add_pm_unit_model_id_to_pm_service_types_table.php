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
        Schema::table('pm_service_types', function (Blueprint $table) {
            $table->foreignUuid('pm_unit_model_id')->nullable()->after('name')->constrained('pm_unit_models')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pm_service_types', function (Blueprint $table) {
            $table->dropForeign(['pm_unit_model_id']);
            $table->dropColumn('pm_unit_model_id');
        });
    }
};
