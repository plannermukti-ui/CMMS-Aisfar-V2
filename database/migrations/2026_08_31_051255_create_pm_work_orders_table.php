<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_work_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('schedule_id')->constrained('pm_service_schedules')->cascadeOnDelete();
            $table->foreignUuid('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->decimal('hm_km_at_execution', 12, 2)->nullable()->comment('Nilai HM/KM saat WO dijalankan');
            $table->date('execution_date')->nullable()->comment('Tanggal eksekusi/service');
            $table->text('notes')->nullable();
            $table->string('status')->default('generated'); // 'generated', 'in_progress', 'completed'
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('created_at');
            $table->index('schedule_id');
            $table->index('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_work_orders');
    }
};
