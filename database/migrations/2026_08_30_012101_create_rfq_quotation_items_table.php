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
        Schema::create('rfq_quotation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rfq_quotation_id')->index();
            $table->uuid('purchase_request_item_id')->index();
            $table->string('status')->nullable()->comment('OEM, Genuine, OCM, dll');
            $table->decimal('qty_ready', 10, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rfq_quotation_id')->references('id')->on('rfq_quotations')->cascadeOnDelete();
            $table->foreign('purchase_request_item_id')->references('id')->on('purchase_request_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfq_quotation_items');
    }
};
