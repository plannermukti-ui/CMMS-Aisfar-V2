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
        Schema::table('work_order_tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('work_order_tasks', 'component')) {
                $table->string('component')->nullable()->after('problem_title');
            }
            if (! Schema::hasColumn('work_order_tasks', 'reff_component_id')) {
                $table->foreignUuid('reff_component_id')->nullable()->after('component')->constrained('reff_components')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('work_order_tasks', 'reff_component_id')) {
                $table->dropForeign(['reff_component_id']);
                $table->dropColumn('reff_component_id');
            }
            if (Schema::hasColumn('work_order_tasks', 'component')) {
                $table->dropColumn('component');
            }
        });
    }
};
