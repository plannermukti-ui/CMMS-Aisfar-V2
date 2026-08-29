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
        // 1. Master Vendors
        Schema::create('vendors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('npwp')->nullable();
            $table->string('term_of_payment')->default('Net 30');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Master Parts / Inventory
        Schema::create('parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('part_number')->unique();
            $table->string('name');
            $table->string('category')->default('Filter');
            $table->string('uom')->default('Pcs');
            $table->decimal('stock_on_hand', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(1);
            $table->decimal('max_stock', 10, 2)->default(10);
            $table->string('bin_location')->nullable();
            $table->decimal('standard_cost', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Material Orders (MOL)
        Schema::create('material_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('mol_number')->unique();
            $table->date('mol_date');
            $table->foreignUuid('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignUuid('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('submitted'); // 'draft', 'submitted', 'approved', 'issued', 'partially_issued', 'converted_to_pr', 'rejected'
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Material Order Items
        Schema::create('material_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('material_order_id')->constrained('material_orders')->cascadeOnDelete();
            $table->foreignUuid('part_id')->nullable()->constrained('parts')->nullOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->decimal('qty_requested', 8, 2);
            $table->decimal('qty_issued', 8, 2)->default(0);
            $table->string('status')->default('pending'); // 'pending', 'ready_stock', 'out_of_stock', 'issued', 'pr_generated'
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Purchase Requests (PR)
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('pr_number')->unique();
            $table->date('pr_date');
            $table->foreignUuid('material_order_id')->nullable()->constrained('material_orders')->nullOnDelete();
            $table->foreignUuid('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('priority')->default('medium');
            $table->date('required_date')->nullable();
            $table->string('status')->default('draft'); // 'draft', 'submitted', 'approved', 'rfq_created', 'po_created', 'rejected'
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Purchase Request Items
        Schema::create('purchase_request_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignUuid('part_id')->nullable()->constrained('parts')->nullOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->decimal('quantity', 8, 2);
            $table->string('uom')->default('Pcs');
            $table->decimal('estimated_unit_price', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. RFQ Quotations (Vendor Comparisons)
        Schema::create('rfq_quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('rfq_number')->unique();
            $table->foreignUuid('purchase_request_id')->constrained('purchase_requests')->cascadeOnDelete();
            $table->foreignUuid('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->string('quotation_number')->nullable();
            $table->decimal('subtotal_dpp', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('ppn_percentage', 5, 2)->default(11.00);
            $table->decimal('ppn_amount', 14, 2)->default(0);
            $table->decimal('shipping_cost', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->unsignedInteger('delivery_lead_time_days')->default(3);
            $table->boolean('is_selected')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 8. Purchase Orders (PO)
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('po_number')->unique();
            $table->date('po_date');
            $table->foreignUuid('purchase_request_id')->nullable()->constrained('purchase_requests')->nullOnDelete();
            $table->foreignUuid('rfq_quotation_id')->nullable()->constrained('rfq_quotations')->nullOnDelete();
            $table->foreignUuid('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->date('delivery_target_date')->nullable();
            $table->decimal('subtotal_dpp', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('ppn_percentage', 5, 2)->default(11.00);
            $table->decimal('ppn_amount', 14, 2)->default(0);
            $table->decimal('shipping_cost', 14, 2)->default(0);
            $table->decimal('grand_total', 14, 2)->default(0);
            $table->string('payment_terms')->default('Net 30');
            $table->string('status')->default('draft'); // 'draft', 'approved', 'sent_to_vendor', 'do_created', 'received', 'cancelled'
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 9. Delivery Orders (DO - Pengiriman to HO / Site)
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('do_number')->unique();
            $table->date('do_date');
            $table->foreignUuid('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
            $table->string('origin_location')->default('Vendor Warehouse');
            $table->foreignUuid('destination_site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('destination_location_name')->default('Site Workshop');
            $table->string('expedition_name')->nullable();
            $table->string('vehicle_plate_number')->nullable();
            $table->string('tracking_number')->nullable();
            $table->dateTime('departure_date')->nullable();
            $table->dateTime('estimated_arrival_date')->nullable();
            $table->dateTime('actual_arrival_date')->nullable();
            $table->string('status')->default('in_transit'); // 'in_transit', 'arrived', 'received'
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 10. Delivery Order Items
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('delivery_order_id')->constrained('delivery_orders')->cascadeOnDelete();
            $table->foreignUuid('part_id')->nullable()->constrained('parts')->nullOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->decimal('qty_shipped', 8, 2);
            $table->string('uom')->default('Pcs');
            $table->timestamps();
            $table->softDeletes();
        });

        // 11. Goods Receipts (GR - Penerimaan Gudang)
        Schema::create('goods_receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('gr_number')->unique();
            $table->date('gr_date');
            $table->foreignUuid('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignUuid('delivery_order_id')->nullable()->constrained('delivery_orders')->nullOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('delivery_order_number')->nullable();
            $table->foreignUuid('received_by_id')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 12. Goods Receipt Items
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('goods_receipt_id')->constrained('goods_receipts')->cascadeOnDelete();
            $table->foreignUuid('part_id')->nullable()->constrained('parts')->nullOnDelete();
            $table->string('part_number');
            $table->string('part_name');
            $table->decimal('qty_received', 8, 2);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
        Schema::dropIfExists('goods_receipts');
        Schema::dropIfExists('delivery_order_items');
        Schema::dropIfExists('delivery_orders');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('rfq_quotations');
        Schema::dropIfExists('purchase_request_items');
        Schema::dropIfExists('purchase_requests');
        Schema::dropIfExists('material_order_items');
        Schema::dropIfExists('material_orders');
        Schema::dropIfExists('parts');
        Schema::dropIfExists('vendors');
    }
};
