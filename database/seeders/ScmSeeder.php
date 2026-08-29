<?php

namespace Database\Seeders;

use App\Models\Part;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ScmSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Vendors
        $vendors = [
            [
                'code' => 'VND-001',
                'name' => 'PT United Tractors Tbk',
                'contact_person' => 'Bambang Sudibyo',
                'phone' => '081299887766',
                'email' => 'sales@unitedtractors.com',
                'address' => 'Jl. Raya Bekasi Km 22, Cakung, Jakarta Timur',
                'npwp' => '01.345.678.9-092.000',
                'term_of_payment' => 'Net 30',
                'bank_name' => 'BCA',
                'bank_account_number' => '5420198821',
                'bank_account_holder' => 'PT United Tractors Tbk',
            ],
            [
                'code' => 'VND-002',
                'name' => 'PT Trakindo Utama',
                'contact_person' => 'Hendro Wijaya',
                'phone' => '081388776655',
                'email' => 'info@trakindo.co.id',
                'address' => 'Jl. Cilandak KKO No. 1, Jakarta Selatan',
                'npwp' => '01.987.654.3-015.000',
                'term_of_payment' => 'Net 30',
                'bank_name' => 'Mandiri',
                'bank_account_number' => '1270008899123',
                'bank_account_holder' => 'PT Trakindo Utama',
            ],
            [
                'code' => 'VND-003',
                'name' => 'PT Hexindo Adiperkasa Tbk',
                'contact_person' => 'Suryanto Pratama',
                'phone' => '081122334455',
                'email' => 'contact@hexindo-tbk.co.id',
                'address' => 'Kawasan Industri Pulogadung, Jakarta Timur',
                'npwp' => '01.222.333.4-041.000',
                'term_of_payment' => 'Net 14',
                'bank_name' => 'BNI',
                'bank_account_number' => '0981234567',
                'bank_account_holder' => 'PT Hexindo Adiperkasa Tbk',
            ],
        ];

        foreach ($vendors as $v) {
            Vendor::firstOrCreate(['code' => $v['code']], $v);
        }

        // 2. Parts
        $parts = [
            [
                'part_number' => 'FLT-ENG-001',
                'name' => 'Fuel Filter Element Komatsu HD785',
                'category' => 'Filter',
                'uom' => 'Pcs',
                'stock_on_hand' => 8,
                'min_stock' => 3,
                'max_stock' => 20,
                'bin_location' => 'Rack A-01-02',
                'standard_cost' => 1250000,
            ],
            [
                'part_number' => 'FLT-OIL-002',
                'name' => 'Engine Oil Filter CAT 777D',
                'category' => 'Filter',
                'uom' => 'Pcs',
                'stock_on_hand' => 0, // Stock out example
                'min_stock' => 2,
                'max_stock' => 15,
                'bin_location' => 'Rack A-02-01',
                'standard_cost' => 850000,
            ],
            [
                'part_number' => 'HYD-SEAL-003',
                'name' => 'Main Hydraulic Cylinder Seal Kit PC2000',
                'category' => 'Hydraulic',
                'uom' => 'Set',
                'stock_on_hand' => 2,
                'min_stock' => 2,
                'max_stock' => 10,
                'bin_location' => 'Rack B-03-04',
                'standard_cost' => 4500000,
            ],
            [
                'part_number' => 'ENG-INJ-004',
                'name' => 'Fuel Injector Nozzle Assembly C15',
                'category' => 'Engine',
                'uom' => 'Pcs',
                'stock_on_hand' => 1,
                'min_stock' => 4,
                'max_stock' => 12,
                'bin_location' => 'Rack C-01-01',
                'standard_cost' => 9800000,
            ],
            [
                'part_number' => 'LUB-15W40-005',
                'name' => 'Heavy Duty Diesel Engine Oil 15W-40',
                'category' => 'Lubricant',
                'uom' => 'Drum',
                'stock_on_hand' => 15,
                'min_stock' => 5,
                'max_stock' => 30,
                'bin_location' => 'Zone Lubricant 1',
                'standard_cost' => 6200000,
            ],
        ];

        foreach ($parts as $p) {
            Part::firstOrCreate(['part_number' => $p['part_number']], $p);
        }
    }
}
