<?php

use App\Models\Part;
use App\Models\Site;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('part_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('part_id')->constrained('parts')->cascadeOnDelete();
            $table->foreignUuid('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('warehouse_name')->default('Gudang Utama');
            $table->string('rack_location');
            $table->decimal('stock_qty', 10, 2)->default(0);
            $table->boolean('is_primary')->default(true);
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Populate initial part locations for existing parts
        $parts = DB::table('parts')->get();
        $sites = DB::table('sites')->get();
        $firstSiteId = $sites->first()->id ?? null;
        $secondSiteId = $sites->skip(1)->first()->id ?? null;

        foreach ($parts as $part) {
            $baseRack = ! empty($part->bin_location) ? $part->bin_location : 'Rak A-01';
            $primaryQty = (float) $part->stock_on_hand;

            // Primary Location
            DB::table('part_locations')->insert([
                'id' => (string) Str::uuid(),
                'part_id' => $part->id,
                'site_id' => $firstSiteId,
                'warehouse_name' => 'Gudang Utama',
                'rack_location' => $baseRack,
                'stock_qty' => $primaryQty,
                'is_primary' => true,
                'notes' => 'Rak Penyimpanan Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Secondary Location if second site exists or secondary buffer rack
            if ($secondSiteId) {
                DB::table('part_locations')->insert([
                    'id' => (string) Str::uuid(),
                    'part_id' => $part->id,
                    'site_id' => $secondSiteId,
                    'warehouse_name' => 'Gudang Site / Workshop',
                    'rack_location' => 'Rak B-02 (Buffer)',
                    'stock_qty' => 0,
                    'is_primary' => false,
                    'notes' => 'Stok Cadangan / Buffer Site',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_locations');
    }
};
