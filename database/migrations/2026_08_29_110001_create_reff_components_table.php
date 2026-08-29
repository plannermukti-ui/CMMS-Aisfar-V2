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
        Schema::create('reff_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('Mechanical'); // Mechanical, Electrical, Hydraulic, Structure, Powertrain
            $table->json('equipment_types')->nullable(); // null for all, or ['Excavator', 'Dozer']
            $table->text('description')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reff_components');
    }
};
