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
        Schema::create('plant_component_condition_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ccr_number')->unique();
            $table->date('ccr_date');
            $table->foreignUuid('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->foreignUuid('component_id')->nullable()->constrained('plant_components')->nullOnDelete();
            $table->string('component_name');
            $table->decimal('current_unit_hm', 10, 2)->nullable();
            $table->decimal('component_running_hours', 10, 2)->nullable();
            $table->decimal('wear_percentage', 5, 2)->default(0); // e.g. 75.00
            $table->string('physical_condition')->default('fair_wear'); // 'good', 'fair_wear', 'critical_wear', 'damaged'
            $table->string('leakage_status')->default('none'); // 'none', 'minor_sweating', 'dripping', 'severe_stream'
            $table->string('noise_vibration_status')->default('normal'); // 'normal', 'abnormal_noise', 'high_vibration'
            $table->string('oil_contamination_status')->default('clean'); // 'clean', 'slight_metal', 'burned_dark', 'water_emulsified'
            $table->text('findings_description')->nullable();
            $table->string('recommendation')->default('continue_run'); // 'continue_run', 'monitor_next_service', 'schedule_changeout', 'immediate_replace', 'rebuild_overhaul', 'scrap'
            $table->decimal('estimated_remaining_hours', 8, 2)->nullable();
            $table->foreignUuid('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('attachment_photos')->nullable();
            $table->string('status')->default('submitted'); // 'draft', 'submitted', 'reviewed_planner', 'approved'
            $table->string('action_taken')->default('none'); // 'none', 'work_order_created', 'mol_created', 'osr_created'
            $table->foreignUuid('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
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
        Schema::dropIfExists('plant_component_condition_reports');
    }
};
