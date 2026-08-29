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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wo_number')->unique();
            $table->date('wo_date');
            $table->string('wo_type')->default('corrective'); // 'preventive', 'corrective', 'breakdown', 'inspection', 'overhaul'
            $table->string('priority')->default('medium'); // 'low', 'medium', 'high', 'emergency'
            $table->string('status')->default('open'); // 'draft', 'open', 'in_progress', 'waiting_part', 'completed', 'closed', 'cancelled'

            // Asset Relations
            $table->foreignUuid('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->decimal('current_hm', 10, 2)->nullable();
            $table->decimal('current_km', 10, 2)->nullable();

            // Personnel Relations
            $table->foreignUuid('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Job Details
            $table->string('job_title');
            $table->text('problem_description')->nullable();
            $table->text('action_taken')->nullable();
            $table->string('root_cause')->nullable();

            // Timeline & Durations
            $table->dateTime('scheduled_start_date')->nullable();
            $table->dateTime('scheduled_end_date')->nullable();
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->decimal('downtime_hours', 8, 2)->default(0);
            $table->decimal('total_labor_hours', 8, 2)->default(0);

            // Media & Attachments
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->string('attachment_file')->nullable();

            // Standard System Columns
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('work_order_mechanics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('hours_spent', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['work_order_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_mechanics');
        Schema::dropIfExists('work_orders');
    }
};
