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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('purchase_order_id')->index();
            $table->uuid('purchase_request_item_id')->nullable()->index();
            $table->uuid('rfq_quotation_item_id')->nullable()->index();
            $table->uuid('part_id')->nullable()->index();
            $table->string('part_number')->nullable();
            $table->string('part_name')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('uom')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
            // Not making PR/RFQ items strict foreign keys with cascade since POs might outlive them or be independent sometimes, 
            // but normally they would be. Let's add them for referential integrity.
            $table->foreign('purchase_request_item_id')->references('id')->on('purchase_request_items')->nullOnDelete();
            $table->foreign('rfq_quotation_item_id')->references('id')->on('rfq_quotation_items')->nullOnDelete();
            $table->foreign('part_id')->references('id')->on('parts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
