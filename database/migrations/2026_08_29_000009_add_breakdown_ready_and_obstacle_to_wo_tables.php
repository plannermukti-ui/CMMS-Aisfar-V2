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
        // 1. Update work_orders table
        Schema::table('work_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('work_orders', 'breakdown_at')) {
                $table->dateTime('breakdown_at')->nullable()->after('wo_date');
            }
            if (! Schema::hasColumn('work_orders', 'ready_at')) {
                $table->dateTime('ready_at')->nullable()->after('breakdown_at');
            }
            if (! Schema::hasColumn('work_orders', 'unit_status')) {
                $table->string('unit_status')->default('breakdown')->after('status'); // 'ready', 'breakdown', 'in_progress', 'standby', 'scheduled_maintenance', 'accident'
            }
            if (! Schema::hasColumn('work_orders', 'is_opportunity')) {
                $table->boolean('is_opportunity')->default(false)->after('unit_status');
            }
        });

        // 2. Update work_order_tasks table
        Schema::table('work_order_tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('work_order_tasks', 'breakdown_at')) {
                $table->dateTime('breakdown_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('work_order_tasks', 'ready_at')) {
                $table->dateTime('ready_at')->nullable()->after('breakdown_at');
            }
            if (! Schema::hasColumn('work_order_tasks', 'downtime_hours')) {
                $table->decimal('downtime_hours', 8, 2)->default(0)->after('ready_at');
            }
        });

        // 3. Update work_order_subtasks table
        Schema::table('work_order_subtasks', function (Blueprint $table) {
            if (! Schema::hasColumn('work_order_subtasks', 'breakdown_at')) {
                $table->dateTime('breakdown_at')->nullable()->after('labor_hours');
            }
            if (! Schema::hasColumn('work_order_subtasks', 'ready_at')) {
                $table->dateTime('ready_at')->nullable()->after('breakdown_at');
            }
            if (! Schema::hasColumn('work_order_subtasks', 'obstacle')) {
                $table->string('obstacle')->default('none')->after('ready_at'); // 'none', 'waiting_part', 'waiting_manpower', 'waiting_tool', 'waiting_weather', 'waiting_approval', 'waiting_location', 'waiting_external'
            }
            if (! Schema::hasColumn('work_order_subtasks', 'obstacle_notes')) {
                $table->text('obstacle_notes')->nullable()->after('obstacle');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_subtasks', function (Blueprint $table) {
            $table->dropColumn(['breakdown_at', 'ready_at', 'obstacle', 'obstacle_notes']);
        });

        Schema::table('work_order_tasks', function (Blueprint $table) {
            $table->dropColumn(['breakdown_at', 'ready_at', 'downtime_hours']);
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn(['breakdown_at', 'ready_at', 'unit_status', 'is_opportunity']);
        });
    }
};
