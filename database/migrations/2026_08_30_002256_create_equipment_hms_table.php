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
        Schema::create('equipment_hms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('equipment_id');
            $table->date('date');
            $table->integer('hm_value');
            $table->boolean('is_interpolated')->default(false)->comment('True jika data hasil kalkulasi (interpolasi)');
            $table->string('source')->nullable()->comment('manual, excel, work_order, interpolated');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');
            // An equipment can only have 1 HM record per day
            $table->unique(['equipment_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_hms');
    }
};
