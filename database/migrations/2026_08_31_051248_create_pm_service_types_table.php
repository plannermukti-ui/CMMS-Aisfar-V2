<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pm_service_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique(); // e.g., "250 Hour Service", "5000 KM Service"
            $table->string('measurement_type')->default('hm'); // 'hm' or 'km'
            $table->unsignedInteger('interval_value')->comment('Nilai interval tetap: 250/500/1000/2000 (HM) atau 5000/10000/20000/40000 (KM)');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // 'active', 'inactive'
            $table->softDeletes();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('created_at');
            $table->index('status');
            $table->index('measurement_type');
            $table->index('interval_value');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pm_service_types');
    }
};
