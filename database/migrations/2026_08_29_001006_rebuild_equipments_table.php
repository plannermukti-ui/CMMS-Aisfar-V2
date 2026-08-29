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
        Schema::dropIfExists('equipments');

        Schema::create('equipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('unit')->unique();
            $table->integer('no')->nullable();
            $table->string('status')->default('Active'); // Active/Deactive
            $table->foreignUuid('reff_equip_id')->nullable()->constrained('reff_equips')->nullOnDelete();
            $table->string('sn_unit')->nullable();
            $table->string('engine_model')->nullable();
            $table->string('sn_engine')->nullable();
            $table->string('eqp_capacity')->nullable();
            $table->string('no_police')->nullable();
            $table->string('attachment')->nullable();
            $table->string('hp_kw')->nullable();
            $table->integer('year_build')->nullable();
            $table->date('date_receive')->nullable();
            $table->foreignUuid('site_id')->nullable()->constrained('sites')->nullOnDelete();

            $table->text('remarks')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
