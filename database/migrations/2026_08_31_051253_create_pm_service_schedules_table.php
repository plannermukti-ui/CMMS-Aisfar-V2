<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_service_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->foreignUuid('service_type_id')->constrained('pm_service_types')->cascadeOnDelete();
            $table->decimal('last_executed_hm_km', 12, 2)->nullable()->comment('Nilai HM/KM terakhir saat service dilakukan');
            $table->date('last_executed_date')->nullable()->comment('Tanggal terakhir service dilakukan');
            $table->decimal('next_target_hm_km', 12, 2)->nullable()->comment('Target HM/KM berikutnya (pembulatan tetap)');
            $table->date('next_plan_date')->nullable()->comment('Estimasi tanggal pencapaian next target');
            $table->unsignedInteger('remain_days')->nullable()->comment('Sisa hari sampai next_plan_date');
            $table->string('status')->default('pending'); // 'pending', 'due_soon', 'overdue', 'completed', 'cancelled'
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['equipment_id', 'service_type_id']);
            $table->index('created_at');
            $table->index('status');
            $table->index('next_target_hm_km');
            $table->index('next_plan_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_service_schedules');
    }
};
