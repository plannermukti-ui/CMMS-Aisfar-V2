<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('departments');
        Schema::dropIfExists('positions');
    }

    public function down(): void
    {
        // We will not recreate them in down() as they are replaced by reff_users
    }
};
