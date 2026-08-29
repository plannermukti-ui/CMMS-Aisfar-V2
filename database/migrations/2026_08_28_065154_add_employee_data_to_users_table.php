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
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('department_id')->nullable()->after('email');
            $table->uuid('position_id')->nullable()->after('department_id');
            $table->string('nik')->nullable()->after('position_id');
            $table->year('join_year')->nullable()->after('nik');
            $table->date('date_of_birth')->nullable()->after('join_year');
            $table->string('phone')->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('phone');
            $table->enum('gender', ['L', 'P'])->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'department_id',
                'position_id',
                'nik',
                'join_year',
                'date_of_birth',
                'phone',
                'address',
                'gender',
            ]);
        });
    }
};
