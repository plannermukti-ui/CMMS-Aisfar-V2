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
        // 1. Add part_id to work_order_subtask_spareparts if not exists
        if (Schema::hasTable('work_order_subtask_spareparts') && ! Schema::hasColumn('work_order_subtask_spareparts', 'part_id')) {
            Schema::table('work_order_subtask_spareparts', function (Blueprint $table) {
                $table->foreignUuid('part_id')->nullable()->after('work_order_subtask_id')->constrained('parts')->nullOnDelete();
            });
        }

        // 2. Stock Opnames
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('opname_number')->unique();
            $table->date('opname_date');
            $table->foreignUuid('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignUuid('conducted_by_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('draft'); // 'draft', 'submitted', 'approved', 'rejected'
            $table->foreignUuid('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->text('discrepancy_reason')->nullable();
            $table->string('berita_acara_number')->nullable()->unique();
            $table->unsignedInteger('total_system_items')->default(0);
            $table->decimal('total_variance_qty', 10, 2)->default(0);
            $table->decimal('total_variance_value', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Stock Opname Items
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignUuid('part_id')->constrained('parts')->cascadeOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->string('uom')->default('Pcs');
            $table->decimal('system_stock', 10, 2)->default(0);
            $table->decimal('physical_stock', 10, 2)->default(0);
            $table->decimal('difference_qty', 10, 2)->default(0);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->decimal('variance_cost', 14, 2)->default(0);
            $table->text('discrepancy_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');

        if (Schema::hasTable('work_order_subtask_spareparts') && Schema::hasColumn('work_order_subtask_spareparts', 'part_id')) {
            Schema::table('work_order_subtask_spareparts', function (Blueprint $table) {
                $table->dropForeign(['part_id']);
                $table->dropColumn('part_id');
            });
        }
    }
};
