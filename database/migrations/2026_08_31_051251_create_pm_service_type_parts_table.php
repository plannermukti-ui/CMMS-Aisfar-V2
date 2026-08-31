<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_service_type_parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_type_task_id')->constrained('pm_service_type_tasks')->cascadeOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->string('unit')->default('Pcs'); // 'Pcs', 'Set', 'Liter', 'Box'
            $table->string('action_type')->default('replace'); // 'replace', 'check', 'top_up'
            $table->text('remarks')->nullable();
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('created_at');
            $table->index('service_type_task_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_service_type_parts');
    }
};
