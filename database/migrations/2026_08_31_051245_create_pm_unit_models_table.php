<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_unit_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // e.g., "Dozer D85ESS", "Dump Truck CAT 777"
            $table->string('measurement_type')->default('hm'); // 'hm' (Hour Meter) or 'km' (Kilometer)
            $table->decimal('target_usage_per_day', 8, 2)->default(8.00)->comment('Target jam operasi (HM) atau jarak tempuh (KM) per hari');
            $table->string('remarks')->nullable();
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('created_at');
            $table->index('status');
            $table->index('measurement_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_unit_models');
    }
};
