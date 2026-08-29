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
        Schema::create('plant_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('component_code')->unique();
            $table->string('serial_number')->nullable();
            $table->string('name');
            $table->string('component_type')->default('engine'); // 'engine', 'transmission', 'differential', 'hydraulic_pump', 'final_drive', 'undercarriage', 'cylinder', 'other'
            $table->string('brand_model')->nullable();
            $table->foreignUuid('equipment_id')->nullable()->constrained('equipments')->nullOnDelete();
            $table->string('position')->nullable(); // e.g. Front-Left, Main, In Workshop
            $table->string('status')->default('ready_spare'); // 'installed', 'ready_spare', 'in_repair_workshop', 'in_outside_repair', 'scrapped'
            $table->decimal('accumulated_hours', 10, 2)->default(0);
            $table->decimal('target_life_hours', 10, 2)->default(10000);
            $table->decimal('installed_at_hm', 10, 2)->nullable();
            $table->date('installed_date')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('plant_component_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('component_id')->constrained('plant_components')->cascadeOnDelete();
            $table->foreignUuid('from_equipment_id')->nullable()->constrained('equipments')->nullOnDelete();
            $table->foreignUuid('to_equipment_id')->nullable()->constrained('equipments')->nullOnDelete();
            $table->string('movement_type'); // 'install', 'remove', 'transfer_to_workshop', 'dispatch_outside', 'receive_outside', 'scrap'
            $table->dateTime('movement_date');
            $table->decimal('equipment_hm', 10, 2)->nullable();
            $table->decimal('component_hours_at_movement', 10, 2)->nullable();
            $table->foreignUuid('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_component_movements');
        Schema::dropIfExists('plant_components');
    }
};
