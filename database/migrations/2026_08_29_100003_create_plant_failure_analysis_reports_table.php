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
        Schema::create('plant_failure_analysis_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('far_number')->unique();
            $table->date('incident_date');
            $table->foreignUuid('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->foreignUuid('component_id')->nullable()->constrained('plant_components')->nullOnDelete();
            $table->foreignUuid('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignUuid('investigator_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('unit_hm_at_failure', 10, 2)->nullable();
            $table->decimal('component_hm_at_failure', 10, 2)->nullable();
            $table->string('failure_type')->default('premature_failure'); // 'premature_failure', 'catastrophic_breakdown', 'fatigue_fracture', 'lubrication_failure', 'overheating', 'operational_misuse', 'assembly_error', 'wear_out'
            $table->string('failure_title');
            $table->text('problem_statement')->nullable();
            $table->text('failure_symptoms')->nullable();
            $table->json('root_cause_5why')->nullable();
            $table->json('fishbone_factors')->nullable();
            $table->text('root_cause_summary')->nullable();
            $table->text('direct_cause')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->text('preventive_actions')->nullable();
            $table->json('attachments')->nullable();
            $table->decimal('cost_impact_estimate', 15, 2)->default(0);
            $table->decimal('downtime_hours_estimate', 8, 2)->default(0);
            $table->string('status')->default('submitted'); // 'draft', 'under_investigation', 'review_manager', 'closed'
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
        Schema::dropIfExists('plant_failure_analysis_reports');
    }
};
