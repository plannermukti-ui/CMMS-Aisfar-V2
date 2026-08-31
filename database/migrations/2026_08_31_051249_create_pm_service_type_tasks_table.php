<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_service_type_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_type_id')->constrained('pm_service_types')->cascadeOnDelete();
            $table->string('task_title'); // e.g., "Ganti Oli Mesin", "Cek Tegangan Track"
            $table->unsignedInteger('task_order')->default(1);
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('created_at');
            $table->index('service_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_service_type_tasks');
    }
};
