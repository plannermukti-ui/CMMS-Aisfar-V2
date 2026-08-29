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
        // 1. Level 2: Tasks (Problems)
        Schema::create('work_order_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->text('problem_title');
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('task_order')->default(1);
            $table->string('status')->default('open'); // 'open', 'in_progress', 'waiting_part', 'completed', 'cancelled'
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Level 3: Subtasks (Actions)
        Schema::create('work_order_subtasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('work_order_task_id')->constrained('work_order_tasks')->cascadeOnDelete();
            $table->text('action_title');
            $table->unsignedInteger('subtask_order')->default(1);
            $table->foreignUuid('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // 'pending', 'in_progress', 'waiting_part', 'completed'
            $table->decimal('labor_hours', 8, 2)->default(0);
            $table->dateTime('actual_start_time')->nullable();
            $table->dateTime('actual_end_time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Level 4a: Subtask Mechanics Assignment Pivot
        Schema::create('work_order_subtask_mechanics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('work_order_subtask_id')->constrained('work_order_subtasks')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('hours_spent', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['work_order_subtask_id', 'user_id']);
        });

        // 4. Level 4b: Subtask Spareparts Usage
        Schema::create('work_order_subtask_spareparts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('work_order_subtask_id')->constrained('work_order_subtasks')->cascadeOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->string('unit')->default('Pcs'); // 'Pcs', 'Set', 'Liter', 'Meter', 'Box'
            $table->string('action_type')->default('replace'); // 'replace', 'swap', 'repair'
            $table->string('status')->default('installed'); // 'installed', 'waiting_part', 'cancelled'
            $table->string('source_unit')->nullable(); // For cannibal/swap source unit
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_subtask_spareparts');
        Schema::dropIfExists('work_order_subtask_mechanics');
        Schema::dropIfExists('work_order_subtasks');
        Schema::dropIfExists('work_order_tasks');
    }
};
