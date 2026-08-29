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
        Schema::create('plant_outside_repairs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('osr_number')->unique();
            $table->date('order_date');
            $table->foreignUuid('equipment_id')->nullable()->constrained('equipments')->nullOnDelete();
            $table->foreignUuid('component_id')->nullable()->constrained('plant_components')->nullOnDelete();
            $table->foreignUuid('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignUuid('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('item_description');
            $table->text('scope_of_work')->nullable();
            $table->string('reason_for_outside')->default('lack_of_specialized_tool'); // 'lack_of_specialized_tool', 'lack_of_machining_equipment', 'overload_workshop', 'warranty_claim', 'specialized_calibration'
            $table->date('dispatch_date')->nullable();
            $table->date('estimated_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->string('delivery_letter_number')->nullable();
            $table->string('received_letter_number')->nullable();
            $table->decimal('estimated_cost', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->integer('warranty_period_months')->default(6);
            $table->integer('warranty_period_hours')->default(1000);
            $table->string('status')->default('dispatched'); // 'draft', 'dispatched', 'vendor_inspecting', 'quotation_approved', 'in_progress', 'testing_qc', 'received_at_site', 'closed', 'rejected_warranty'
            $table->boolean('qc_passed')->default(false);
            $table->text('qc_notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_outside_repairs');
    }
};
