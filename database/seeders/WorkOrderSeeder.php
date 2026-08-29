<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\ReffComponent;
use App\Models\Site;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderSubtask;
use App\Models\WorkOrderSubtaskSparepart;
use App\Models\WorkOrderTask;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $adminUser = User::first();
        $siteCentral = Site::where('site_code', 'SITE-BLI')->first() ?? Site::first();
        $siteWest = Site::where('site_code', 'SITE-KUT')->first() ?? $siteCentral;

        $findEquipment = function ($unit) {
            return Equipment::where('unit', $unit)->first();
        };

        $findComponentId = function ($name) {
            return ReffComponent::where('name', 'LIKE', "%{$name}%")->value('id');
        };

        // 40 Comprehensive, Varied Real-World Work Orders (August 3 to August 23, 2026)
        $workOrderData = [
            // -------------------------------------------------------------
            // 1. OHT Heavy Haulers (3 - 7 Aug 2026)
            // -------------------------------------------------------------
            [
                'unit' => 'OHT-777-01',
                'wo_number' => 'WO-2608-0001',
                'date' => '2026-08-03',
                'breakdown_time' => '2026-08-03 06:30:00',
                'ready_time' => '2026-08-03 14:45:00',
                'wo_type' => 'breakdown',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 3840.5,
                'job_title' => 'Tekanan Front Suspension Strut Kiri Drop & Retarder Hunting',
                'root_cause' => 'Seal failure pada nitrogen charge valve front left suspension',
                'action_taken' => 'Recharging dry nitrogen & resealing strut charge valve',
                'tasks' => [
                    [
                        'title' => 'Suspension Cylinder Depan Kiri Kempes (Low Nitrogen Pressure)',
                        'component' => 'Suspension System (Struts / Leaf Springs)',
                        'breakdown_at' => '2026-08-03 06:30:00',
                        'ready_at' => '2026-08-03 12:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Inspeksi kebocoran oil & nitrogen front strut',
                                'obstacle' => 'none',
                                'notes' => 'Terdapat rembesan oli di seal wiper',
                                'parts' => [
                                    ['part_no' => '232-3850', 'part_name' => 'Seal Kit Suspension Cylinder', 'qty' => 1, 'unit' => 'Set'],
                                    ['part_no' => 'HYD-OIL-10W', 'part_name' => 'Cat HYDO Advanced 10W', 'qty' => 15, 'unit' => 'Liter'],
                                ],
                            ],
                            [
                                'action' => 'Charging Nitrogen Gas 350 PSI & Leveling height',
                                'obstacle' => 'none',
                                'notes' => 'Tinggi rod chrome disetel 125mm sesuai spek manual',
                                'parts' => [],
                            ],
                        ],
                    ],
                    [
                        'title' => 'Sensitivitas ARC (Automatic Retarder Control) Lambat',
                        'component' => 'Brake & Retarder System',
                        'breakdown_at' => '2026-08-03 12:00:00',
                        'ready_at' => '2026-08-03 14:45:00',
                        'subtasks' => [
                            [
                                'action' => 'Kalibrasi ARC Solenoid & Pembersihan sensor speed retarder',
                                'obstacle' => 'none',
                                'notes' => 'Pressure retarder stabil di 480 PSI',
                                'parts' => [
                                    ['part_no' => '174-8241', 'part_name' => 'Pressure Sensor Retarder', 'qty' => 1, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'OHT-785-01',
                'wo_number' => 'WO-2608-0002',
                'date' => '2026-08-03',
                'breakdown_time' => '2026-08-03 08:00:00',
                'ready_time' => '2026-08-03 16:30:00',
                'wo_type' => 'preventive',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 2500.0,
                'job_title' => 'Periodical Maintenance Servis 250 Jam & Penggantian Filter Lengkap',
                'root_cause' => 'Jadwal Servis Berkala (PM-250)',
                'action_taken' => 'Ganti Engine Oil, Fuel Filter, Oil Filter, Inspeksi V-Belt',
                'tasks' => [
                    [
                        'title' => 'Servis Rutin PM 250 Hours Engine & Powertrain',
                        'component' => 'Engine System',
                        'breakdown_at' => '2026-08-03 08:00:00',
                        'ready_at' => '2026-08-03 16:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Drain engine oil & replace primary/secondary oil filter',
                                'obstacle' => 'none',
                                'notes' => 'Kondisi oli lama normal tanpa partikel besi',
                                'parts' => [
                                    ['part_no' => '600-211-1340', 'part_name' => 'Engine Oil Filter Cartridge', 'qty' => 4, 'unit' => 'Pcs'],
                                    ['part_no' => '600-319-3550', 'part_name' => 'Fuel Pre-Filter Water Separator', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'ENG-OIL-15W40', 'part_name' => 'Komatsu Engine Oil 15W-40', 'qty' => 120, 'unit' => 'Liter'],
                                ],
                            ],
                            [
                                'action' => 'Inspeksi & Penyetelan Kekencangan Fan Belt & Alternator Belt',
                                'obstacle' => 'none',
                                'notes' => 'Deflection belt 10mm normal',
                                'parts' => [],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'OHT-777-02',
                'wo_number' => 'WO-2608-0003',
                'date' => '2026-08-04',
                'breakdown_time' => '2026-08-04 14:15:00',
                'ready_time' => '2026-08-04 20:30:00',
                'wo_type' => 'breakdown',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 4120.0,
                'job_title' => 'Ban Belakang Kanan Posisi 5 Sobek Terkena Batu Tajam (Side-wall Cut)',
                'root_cause' => 'Tire sidewall cut akibat batuan keras di loading point Pit South',
                'action_taken' => 'Penggantian Ban OTR 27.00R49 Posisi 5 dan balancing torquing',
                'tasks' => [
                    [
                        'title' => 'Pecah / Sobek Ban Posisi 5 (Rear Right Outer)',
                        'component' => 'Wheel, Rim & Tire System',
                        'breakdown_at' => '2026-08-04 14:15:00',
                        'ready_at' => '2026-08-04 20:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Pelepasan ban rusak dan pemasangan ban cadangan OTR 27.00R49',
                                'obstacle' => 'none',
                                'notes' => 'Torque wheel nut 1200 Nm dengan hydraulic torque wrench',
                                'parts' => [
                                    ['part_no' => 'TIRE-27.00R49', 'part_name' => 'OTR Radial Tire 27.00R49 Bridgestone', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => 'O-RING-RIM49', 'part_name' => 'O-Ring Rim 49 Inch', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => 'VALVE-TRJ1175', 'part_name' => 'Large Bore Tire Valve Stem', 'qty' => 1, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // 2. Big Diggers & Excavators (4 - 9 Aug 2026)
            // -------------------------------------------------------------
            [
                'unit' => 'EX-PC8000-01',
                'wo_number' => 'WO-2608-0004',
                'date' => '2026-08-04',
                'breakdown_time' => '2026-08-04 09:00:00',
                'ready_time' => '2026-08-05 03:30:00',
                'wo_type' => 'corrective',
                'priority' => 'emergency',
                'unit_status' => 'ready',
                'current_hm' => 8950.0,
                'job_title' => 'Ganti 4 Pcs Bucket Teeth & Hardfacing Lip Shroud Bucket 42 m³',
                'root_cause' => 'Keausan ekstrim batuan sandstone abrasi tinggi',
                'action_taken' => 'Ganti adapter teeth ESCO Ultralok U60 & pengelasan wear plate',
                'tasks' => [
                    [
                        'title' => 'Keausan Kuku Bucket & Lip Shroud Melebihi Batas Toleransi',
                        'component' => 'Work Attachment (Boom, Arm, Bucket & GET)',
                        'breakdown_at' => '2026-08-04 09:00:00',
                        'ready_at' => '2026-08-05 03:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Pelepasan kuku lama yang aus & pemasangan Tooth ESCO U60HD',
                                'obstacle' => 'none',
                                'notes' => 'Pin locking system terpasang kencang',
                                'parts' => [
                                    ['part_no' => 'ESCO-U60HD', 'part_name' => 'Bucket Tooth Heavy Duty U60HD', 'qty' => 4, 'unit' => 'Pcs'],
                                    ['part_no' => 'ESCO-U60P', 'part_name' => 'Tooth Pin Locking System', 'qty' => 4, 'unit' => 'Pcs'],
                                ],
                            ],
                            [
                                'action' => 'Hardfacing & Welding Liner Plate Hardox 500 di bibir bucket',
                                'obstacle' => 'none',
                                'notes' => 'Welding inspection lolos uji dye penetrant',
                                'parts' => [
                                    ['part_no' => 'WELD-E7018', 'part_name' => 'Kobe Steel Welding Electrode LB-52U', 'qty' => 25, 'unit' => 'Kg'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'EX-390F-01',
                'wo_number' => 'WO-2608-0005',
                'date' => '2026-08-05',
                'breakdown_time' => '2026-08-05 07:15:00',
                'ready_time' => '2026-08-05 13:45:00',
                'wo_type' => 'breakdown',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 3420.0,
                'job_title' => 'Bocor Oli Hidrolik Hose Bucket Cylinder High Pressure',
                'root_cause' => 'Abrasi gesekan hose guard pelindung selang hidrolik',
                'action_taken' => 'Ganti Hydraulic Hose 4-Spiral Wire & Top up oli ISO VG 46',
                'tasks' => [
                    [
                        'title' => 'Semburan Oli Hidrolik pada Hose Bucket Cylinder Head',
                        'component' => 'Main Hydraulic & Pump System',
                        'breakdown_at' => '2026-08-05 07:15:00',
                        'ready_at' => '2026-08-05 13:45:00',
                        'subtasks' => [
                            [
                                'action' => 'Pelepasan hose pecah & crimping hose baru XT-6 ES',
                                'obstacle' => 'none',
                                'notes' => 'Uji tekanan 350 bar tanpa rembesan',
                                'parts' => [
                                    ['part_no' => 'HOSE-XT6-16', 'part_name' => 'Cat XT-6 ES Hydraulic Hose 1 Inch', 'qty' => 3.5, 'unit' => 'Meter'],
                                    ['part_no' => 'FLANGE-16CODE62', 'part_name' => 'Split Flange Fitting Code 62 1"', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'HYD-OIL-46', 'part_name' => 'Hydraulic Oil Tellus S2 M 46', 'qty' => 40, 'unit' => 'Liter'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'EX-EX5500-01',
                'wo_number' => 'WO-2608-0006',
                'date' => '2026-08-06',
                'breakdown_time' => '2026-08-06 10:00:00',
                'ready_time' => '2026-08-07 02:00:00',
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 6700.0,
                'job_title' => 'Penggantian Track Roller No. 4 & 5 Kiri yang Macet (Undercarriage)',
                'root_cause' => 'Floating seal wear mengakibatkan pelumas roller bocor dan bearing macet',
                'action_taken' => 'Jack up unit, lepas track shoe segment, ganti dual track roller',
                'tasks' => [
                    [
                        'title' => 'Track Roller Bawah Kiri No. 4 & 5 Terkunci / Macet',
                        'component' => 'Undercarriage (Track, Roller, Idler, Sprocket)',
                        'breakdown_at' => '2026-08-06 10:00:00',
                        'ready_at' => '2026-08-07 02:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Kendorkan track tensioner & angkat frame track kiri',
                                'obstacle' => 'none',
                                'notes' => 'Gunakan hydraulic jack 100 Ton',
                                'parts' => [],
                            ],
                            [
                                'action' => 'Pemasangan Track Roller Double Flange baru & torquing bolt',
                                'obstacle' => 'none',
                                'notes' => 'Torque bolt M30 grade 12.9 ke 1800 Nm',
                                'parts' => [
                                    ['part_no' => 'HIT-TR-5500', 'part_name' => 'Hitachi EX5500 Track Roller D/F Ass’y', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'BOLT-M30-12.9', 'part_name' => 'High Tensile Bolt M30x120', 'qty' => 8, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // 3. Bulldozers & Heavy Ripper (6 - 11 Aug 2026)
            // -------------------------------------------------------------
            [
                'unit' => 'DZ-D375-01',
                'wo_number' => 'WO-2608-0007',
                'date' => '2026-08-06',
                'breakdown_time' => '2026-08-06 11:30:00',
                'ready_time' => '2026-08-06 17:00:00',
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 4580.0,
                'job_title' => 'Ganti Ripper Tip Shank Tengah & End Bit Blade Dozer yang Tumpul',
                'root_cause' => 'Penetrasi batu overburden keras',
                'action_taken' => 'Ganti 1 set Ripper Tip D375 & Cutting Edge Blade',
                'tasks' => [
                    [
                        'title' => 'Ripper Tip Patah & Cutting Edge Blade Aus Tipis',
                        'component' => 'Work Attachment (Blade & Ripper)',
                        'breakdown_at' => '2026-08-06 11:30:00',
                        'ready_at' => '2026-08-06 17:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Ganti Shank Ripper Protector & Heavy Duty Ripper Tip',
                                'obstacle' => 'none',
                                'notes' => 'Pemasangan pin retainer baru',
                                'parts' => [
                                    ['part_no' => '195-78-71320', 'part_name' => 'Ripper Tip D375A Point', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => '195-78-71111', 'part_name' => 'Shank Protector D375A', 'qty' => 1, 'unit' => 'Pcs'],
                                ],
                            ],
                            [
                                'action' => 'Ganti End Bit Kiri-Kanan Blade Semi-U',
                                'obstacle' => 'none',
                                'notes' => 'Pemberian anti-seize pada plow bolt',
                                'parts' => [
                                    ['part_no' => '195-71-11654', 'part_name' => 'End Bit LH/RH D375 Blade', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'PLOW-BOLT-1IN', 'part_name' => 'Plow Bolt 1 Inch x 3.5 Inch', 'qty' => 14, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'DZ-D10T-01',
                'wo_number' => 'WO-2608-0008',
                'date' => '2026-08-07',
                'breakdown_time' => '2026-08-07 08:30:00',
                'ready_time' => '2026-08-07 15:30:00',
                'wo_type' => 'corrective',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 5210.0,
                'job_title' => 'Penyetelan Track Tensioner Kanan & Penggantian Recoil Seal',
                'root_cause' => 'Grease relief valve track tensioner bocor halus sehingga track kendur',
                'action_taken' => 'Ganti relief valve grease cylinder & pompa gemuk EP-2 ke 120mm sag',
                'tasks' => [
                    [
                        'title' => 'Track Sag Rantai Kanan Terlalu Kendur (Track Slooping)',
                        'component' => 'Undercarriage (Track, Roller, Idler, Sprocket)',
                        'breakdown_at' => '2026-08-07 08:30:00',
                        'ready_at' => '2026-08-07 15:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Inspeksi & Ganti Track Adjuster Valve Fitting',
                                'obstacle' => 'none',
                                'notes' => 'Tekanan gemuk stabil setelah injeksi grease pump',
                                'parts' => [
                                    ['part_no' => '7H-1447', 'part_name' => 'Track Adjuster Valve Fitting', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => 'GREASE-EP2', 'part_name' => 'Cat Ultra 5Moly Grease EP-2', 'qty' => 10, 'unit' => 'Kg'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // 4. Light Vehicles & Mine Patrol (8 - 14 Aug 2026)
            // -------------------------------------------------------------
            [
                'unit' => 'LV-HLX-01',
                'wo_number' => 'WO-2608-0009',
                'date' => '2026-08-08',
                'breakdown_time' => '2026-08-08 07:30:00',
                'ready_time' => '2026-08-08 11:30:00',
                'wo_type' => 'preventive',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 1150.0,
                'job_title' => 'Servis Berkala 10.000 KM & Ganti Kampas Rem Depan Hilux D-Cab',
                'root_cause' => 'Jadwal Servis Berkala LV',
                'action_taken' => 'Ganti Oli Mesin Toyota 5W-30, Filter Oli, & Brake Pad Depan',
                'tasks' => [
                    [
                        'title' => 'Servis 10.000 KM & Brake Pad Replacement',
                        'component' => 'Brake & Retarder System',
                        'breakdown_at' => '2026-08-08 07:30:00',
                        'ready_at' => '2026-08-08 11:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Ganti Brake Pad Disc Brake Depan & Bleeding minyak rem',
                                'obstacle' => 'none',
                                'notes' => 'Ketebalan disc rotor masih 26.5mm (aman)',
                                'parts' => [
                                    ['part_no' => '04465-0K360', 'part_name' => 'Brake Pad Set Front Toyota Hilux', 'qty' => 1, 'unit' => 'Set'],
                                    ['part_no' => 'BRAKE-FLUID-DOT4', 'part_name' => 'Brake Fluid DOT 4 1L', 'qty' => 1, 'unit' => 'Liter'],
                                    ['part_no' => '90915-YZZD2', 'part_name' => 'Oil Filter Toyota 2GD', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => 'ENG-OIL-5W30', 'part_name' => 'Toyota Motor Oil 5W-30 Synthetic', 'qty' => 7.5, 'unit' => 'Liter'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'LV-HLX-02',
                'wo_number' => 'WO-2608-0010',
                'date' => '2026-08-09',
                'breakdown_time' => '2026-08-09 13:00:00',
                'ready_time' => '2026-08-09 16:00:00',
                'wo_type' => 'corrective',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 980.0,
                'job_title' => 'Rotary Lightbar Safety & Buggy Whip LED Mati Total',
                'root_cause' => 'Relay fuse 15A putus akibat getaran kabel ground di rollbar',
                'action_taken' => 'Perbaikan wiring harness rollbar, ganti fuse 15A & LED bulb',
                'tasks' => [
                    [
                        'title' => 'Safety Strobe Light & Whip Flag Tidak Menyala',
                        'component' => 'Electrical & Instrument System',
                        'breakdown_at' => '2026-08-09 13:00:00',
                        'ready_at' => '2026-08-09 16:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Inspeksi jalur kelistrikan rollbar & penggantian fuse relay',
                                'obstacle' => 'none',
                                'notes' => 'Lampu rotary dan bendera LED berfungsi terang',
                                'parts' => [
                                    ['part_no' => 'FUSE-15A-BLADE', 'part_name' => 'Blade Fuse 15 Ampere', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'LED-WHIP-LIGHT', 'part_name' => 'LED Module Whip Flag 12V', 'qty' => 1, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'LV-TRT-01',
                'wo_number' => 'WO-2608-0011',
                'date' => '2026-08-10',
                'breakdown_time' => '2026-08-10 08:00:00',
                'ready_time' => '2026-08-10 13:00:00',
                'wo_type' => 'corrective',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 1420.0,
                'job_title' => 'AC Mobil Tidak Dingin Hanya Blower Angin (Triton 4x4)',
                'root_cause' => 'O-Ring seal suction pipe kompresor AC bocor freon',
                'action_taken' => 'Vacuum system, ganti O-Ring pipa AC, isi Freon R134a & Oli PAG',
                'tasks' => [
                    [
                        'title' => 'Freon AC Habis & Blower Mengeluarkan Udara Hangat',
                        'component' => 'Cabin & Air Conditioning (AC)',
                        'breakdown_at' => '2026-08-10 08:00:00',
                        'ready_at' => '2026-08-10 13:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Leak test dengan nitrogen & pengisian Freon R134a',
                                'obstacle' => 'none',
                                'notes' => 'Suhu kisi AC mencapai 6.5°C sangat dingin',
                                'parts' => [
                                    ['part_no' => 'FREON-R134A', 'part_name' => 'Refrigerant Gas Freon R134a Can', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'ORING-AC-HNBR', 'part_name' => 'HNBR Green O-Ring AC Assortment', 'qty' => 4, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // 5. Motor Graders & Loaders (11 - 16 Aug 2026)
            // -------------------------------------------------------------
            [
                'unit' => 'MG-24M-01',
                'wo_number' => 'WO-2608-0012',
                'date' => '2026-08-11',
                'breakdown_time' => '2026-08-11 07:00:00',
                'ready_time' => '2026-08-11 15:30:00',
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 3100.0,
                'job_title' => 'Penggantian Cutting Edge Moldboard 24ft & Shim Circle Drive Clearance',
                'root_cause' => 'Keausan rutin grading jalan tambang batubara',
                'action_taken' => 'Ganti 4 bilah Cutting Edge Curved 6ft & stel clearance circle pinion',
                'tasks' => [
                    [
                        'title' => 'Cutting Edge Blade 24M Tipis & Circle Gear Ada Backlash',
                        'component' => 'Moldboard, Circle Drive & Drawbar',
                        'breakdown_at' => '2026-08-11 07:00:00',
                        'ready_at' => '2026-08-11 15:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Pelepasan cutting edge lama & pasang Cat Curved Edge 3/4" x 8"',
                                'obstacle' => 'none',
                                'notes' => 'Plow bolt dikencangkan 350 Nm',
                                'parts' => [
                                    ['part_no' => 'CAT-EDGE-6FT', 'part_name' => 'Cat Grader Cutting Edge 6 Foot Curved', 'qty' => 4, 'unit' => 'Pcs'],
                                    ['part_no' => 'PLOW-BOLT-3/4', 'part_name' => 'Plow Bolt 3/4" x 3"', 'qty' => 28, 'unit' => 'Pcs'],
                                ],
                            ],
                            [
                                'action' => 'Penyetelan Shim Pack Top & Bottom Circle Shoes',
                                'obstacle' => 'none',
                                'notes' => 'Clearance disetel 0.8mm sesuai Cat SIS guideline',
                                'parts' => [
                                    ['part_no' => 'SHIM-CIRCLE-24M', 'part_name' => 'Brass Wear Strip & Shim Kit 24M', 'qty' => 1, 'unit' => 'Set'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'WL-WA800-01',
                'wo_number' => 'WO-2608-0013',
                'date' => '2026-08-12',
                'breakdown_time' => '2026-08-12 09:15:00',
                'ready_time' => '2026-08-12 17:45:00',
                'wo_type' => 'breakdown',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 4820.0,
                'job_title' => 'Bocor Oli Steering Cylinder Kiri & Kemudi Terasa Berat',
                'root_cause' => 'Seal rod steering cylinder tergores partikel debu halus',
                'action_taken' => 'Reseal Steering Cylinder LH & Flushing Steering Circuit',
                'tasks' => [
                    [
                        'title' => 'Kebocoran Oli pada Gland Seal Steering Cylinder LH',
                        'component' => 'Steering & Articulation System',
                        'breakdown_at' => '2026-08-12 09:15:00',
                        'ready_at' => '2026-08-12 17:45:00',
                        'subtasks' => [
                            [
                                'action' => 'Dismantle steering cylinder gland & pasang seal kit baru',
                                'obstacle' => 'none',
                                'notes' => 'Rod chrome dipoles dengan amril halus',
                                'parts' => [
                                    ['part_no' => '707-99-68420', 'part_name' => 'Seal Kit Steering Cylinder WA800', 'qty' => 1, 'unit' => 'Set'],
                                    ['part_no' => 'HYD-OIL-46', 'part_name' => 'Hydraulic Oil Tellus S2 M 46', 'qty' => 30, 'unit' => 'Liter'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // 6. Support & Ancillary Equipment (13 - 18 Aug 2026)
            // -------------------------------------------------------------
            [
                'unit' => 'WT-FUSO-01',
                'wo_number' => 'WO-2608-0014',
                'date' => '2026-08-13',
                'breakdown_time' => '2026-08-13 08:30:00',
                'ready_time' => '2026-08-13 14:00:00',
                'wo_type' => 'corrective',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 2940.0,
                'job_title' => 'Impeller Water Cannon Macet Terganjal Kerikil Lumpur',
                'root_cause' => 'Filter saringan hisap bak air tangki robek',
                'action_taken' => 'Bongkar housing pompa centrifugal, bersihkan impeller, ganti strainer',
                'tasks' => [
                    [
                        'title' => 'Pompa Water Cannon Tidak Berputar & Suhu Pompa Panas',
                        'component' => 'Water Cannon & Pumping System',
                        'breakdown_at' => '2026-08-13 08:30:00',
                        'ready_at' => '2026-08-13 14:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Overhaul impeller housing & ganti mechanical shaft seal',
                                'obstacle' => 'none',
                                'notes' => 'Semprotan water cannon mencapai jarak 45 meter',
                                'parts' => [
                                    ['part_no' => 'MECH-SEAL-PUMP', 'part_name' => 'Mechanical Seal Water Pump 3 Inch', 'qty' => 1, 'unit' => 'Set'],
                                    ['part_no' => 'STRAINER-SS-4IN', 'part_name' => 'Stainless Steel Suction Strainer 4"', 'qty' => 1, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'FT-HINO-01',
                'wo_number' => 'WO-2608-0015',
                'date' => '2026-08-14',
                'breakdown_time' => '2026-08-14 10:00:00',
                'ready_time' => '2026-08-14 15:30:00',
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 2150.0,
                'job_title' => 'Kalibrasi Flowmeter Solar TCS & Penggantian Swivel Nozzle Fuel Reel',
                'root_cause' => 'Swivel joint fuel nozzle bocor solar saat pengisian unit OHT',
                'action_taken' => 'Ganti High Flow Fuel Nozzle Wiggins & kalibrasi akurasi meter',
                'tasks' => [
                    [
                        'title' => 'Rembesan Solar pada Nozzle Reel High Flow Wiggins',
                        'component' => 'Fuel Dispenser & Metering System',
                        'breakdown_at' => '2026-08-14 10:00:00',
                        'ready_at' => '2026-08-14 15:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Ganti Nozzle Wiggins Fast Fueling & Swivel Seal',
                                'obstacle' => 'none',
                                'notes' => 'Akurasi meter 99.8% sesuai standar tera BBM',
                                'parts' => [
                                    ['part_no' => 'WIGGINS-ZZ9A1', 'part_name' => 'Wiggins Fast Fueling Nozzle ZZ9A1', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => 'SWIVEL-SEAL-1.5', 'part_name' => 'Viton Swivel Seal Kit 1.5 Inch', 'qty' => 1, 'unit' => 'Set'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'CR-TADANO-01',
                'wo_number' => 'WO-2608-0016',
                'date' => '2026-08-15',
                'breakdown_time' => '2026-08-15 08:00:00',
                'ready_time' => '2026-08-15 16:00:00',
                'wo_type' => 'inspection',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 1890.0,
                'job_title' => 'Inspeksi Kelaikan Wire Rope Crane 50T & Kalibrasi Load Moment (LMI)',
                'root_cause' => 'Inspeksi K3 Berkala Sertifikasi Alat Angkat',
                'action_taken' => 'Uji beban statis 25 Ton, pelumasan wire rope, & cek sensor sudut boom',
                'tasks' => [
                    [
                        'title' => 'Inspeksi Wire Rope & Kalibrasi Safety Cut-Off System',
                        'component' => 'Telescopic Boom & Winch Hoist Crane',
                        'breakdown_at' => '2026-08-15 08:00:00',
                        'ready_at' => '2026-08-15 16:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Visual check wire rope 19mm non-rotating & grease coating',
                                'obstacle' => 'none',
                                'notes' => 'Tidak ditemukan kawat putus (broken wires: 0)',
                                'parts' => [
                                    ['part_no' => 'ROPE-LUBE-SPRAY', 'part_name' => 'Wire Rope Penetrating Lubricant', 'qty' => 4, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'GEN-CAT-01',
                'wo_number' => 'WO-2608-0017',
                'date' => '2026-08-16',
                'breakdown_time' => '2026-08-16 09:00:00',
                'ready_time' => '2026-08-16 13:30:00',
                'wo_type' => 'preventive',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 4200.0,
                'job_title' => 'Servis Rutin Genset 500 kVA Camp Utama & Penggantian Filter Solar',
                'root_cause' => 'Jadwal Servis Berkala Genset',
                'action_taken' => 'Ganti Oli Mesin, Filter Oli, Filter Solar, & Cek Tegangan AVR 380V',
                'tasks' => [
                    [
                        'title' => 'Servis Berkala Engine CAT C13 & Uji Beban AVR Panel',
                        'component' => 'Engine System',
                        'breakdown_at' => '2026-08-16 09:00:00',
                        'ready_at' => '2026-08-16 13:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Ganti Filter Solar & Filter Oli serta Bleeding Fuel Line',
                                'obstacle' => 'none',
                                'notes' => 'Tegangan stabil 380V 3-Phase, Frekuensi 50.1 Hz',
                                'parts' => [
                                    ['part_no' => '1R-1808', 'part_name' => 'Cat Oil Filter Advanced Efficiency', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => '1R-0749', 'part_name' => 'Cat Fuel Filter Secondary', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'ENG-OIL-15W40', 'part_name' => 'Diesel Engine Oil 15W-40', 'qty' => 38, 'unit' => 'Liter'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // -------------------------------------------------------------
            // 7. Recent Fleet Events & In-Progress / Waiting Part (17 - 23 Aug 2026)
            // -------------------------------------------------------------
            [
                'unit' => 'OHT-797-01',
                'wo_number' => 'WO-2608-0018',
                'date' => '2026-08-17',
                'breakdown_time' => '2026-08-17 14:00:00',
                'ready_time' => '2026-08-18 08:30:00',
                'wo_type' => 'breakdown',
                'priority' => 'emergency',
                'unit_status' => 'ready',
                'current_hm' => 6140.0,
                'job_title' => 'Alarm High Exhaust Temperature Turbocharger Bank Kanan (Quad-Turbo)',
                'root_cause' => 'Klem V-Band exhaust turbo No. 3 retak dan bocor gas buang',
                'action_taken' => 'Ganti V-Band Clamp & Gasket Turbocharger High Temp',
                'tasks' => [
                    [
                        'title' => 'Exhaust Leakage pada Turbocharger RH Bank No. 3',
                        'component' => 'Engine System',
                        'breakdown_at' => '2026-08-17 14:00:00',
                        'ready_at' => '2026-08-18 08:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Dismantle heat shield & pasang clamp stainless Inconel baru',
                                'obstacle' => 'none',
                                'notes' => 'EGT normal di bawah 580°C pada beban penuh',
                                'parts' => [
                                    ['part_no' => 'CLAMP-VBAND-5IN', 'part_name' => 'Heavy Duty Inconel V-Band Clamp 5"', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'GASKET-TURBO-C175', 'part_name' => 'High Temp Stainless Turbo Gasket', 'qty' => 2, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'EX-PC800-01',
                'wo_number' => 'WO-2608-0019',
                'date' => '2026-08-18',
                'breakdown_time' => '2026-08-18 08:00:00',
                'ready_time' => '2026-08-18 16:30:00',
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 4920.0,
                'job_title' => 'Penggantian Pin & Bushing Arm to Bucket Sambungan Utama',
                'root_cause' => 'Backlash berlebih mencapai 8mm akibat pelumasan auto lube tersumbat',
                'action_taken' => 'Ekstraksi pin bucket, pasang bushing baru dengan induksi pemanas & shimming',
                'tasks' => [
                    [
                        'title' => 'Play / Speling Berlebih pada Sambungan Bucket Arm Pin',
                        'component' => 'Work Attachment (Boom, Arm, Bucket & GET)',
                        'breakdown_at' => '2026-08-18 08:00:00',
                        'ready_at' => '2026-08-18 16:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Pelepasan Pin dengan hydraulic puller & pasang Bushing 110mm',
                                'obstacle' => 'none',
                                'notes' => 'Gunakan seal dust baru pada kedua sisi link',
                                'parts' => [
                                    ['part_no' => '208-70-13140', 'part_name' => 'Bucket Arm Bushing PC800', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => '208-70-13151', 'part_name' => 'Bucket Joint Pin Hardened', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => '208-70-13160', 'part_name' => 'Dust Seal Ring 110mm', 'qty' => 4, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'OHT-930-01',
                'wo_number' => 'WO-2608-0020',
                'date' => '2026-08-19',
                'breakdown_time' => '2026-08-19 11:00:00',
                'ready_time' => '2026-08-20 04:00:00',
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 5300.0,
                'job_title' => 'Inspeksi & Resealing Wheel Motor Blower AC Drive Powertrain',
                'root_cause' => 'Kontaminasi debu pada filter blower motor traksi listrik',
                'action_taken' => 'Pembersihan blower impeller, ganti pre-cleaner filter & megger test isolasi',
                'tasks' => [
                    [
                        'title' => 'Air Flow Blower Traksi Motor Rendah & Warning Overheat Motor Kanan',
                        'component' => 'Electrical & Instrument System',
                        'breakdown_at' => '2026-08-19 11:00:00',
                        'ready_at' => '2026-08-20 04:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Pembersihan elemen filter udara traksi & Uji tahanan isolasi (Megger)',
                                'obstacle' => 'none',
                                'notes' => 'Insulation resistance > 500 MegaOhm (sangat baik)',
                                'parts' => [
                                    ['part_no' => 'KMT-FILT-AIR-BLW', 'part_name' => 'Traction Blower Air Filter Element', 'qty' => 2, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'DZ-D8T-01',
                'wo_number' => 'WO-2608-0021',
                'date' => '2026-08-20',
                'breakdown_time' => '2026-08-20 07:30:00',
                'ready_time' => '2026-08-20 16:00:00',
                'wo_type' => 'preventive',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 3750.0,
                'job_title' => 'Servis Berkala PM-500 Jam & Ganti Oli Final Drive Dozer D8T',
                'root_cause' => 'Jadwal Servis Berkala PM-500',
                'action_taken' => 'Ganti Oli Mesin, Oli Transmisi, Oli Final Drive LH/RH, & Greasing',
                'tasks' => [
                    [
                        'title' => 'Servis Rutin PM 500 Jam Complete Dozer D8T',
                        'component' => 'Transmission & Torque Converter',
                        'breakdown_at' => '2026-08-20 07:30:00',
                        'ready_at' => '2026-08-20 16:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Drain & refill Transmission Oil TO-4 SAE 30 & Filter',
                                'obstacle' => 'none',
                                'notes' => 'Magnetic plug bersih tanpa serpihan metal',
                                'parts' => [
                                    ['part_no' => '1R-0716', 'part_name' => 'Cat Transmission Oil Filter', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'OIL-TO4-SAE30', 'part_name' => 'Cat TDTO SAE 30 Powertrain Oil', 'qty' => 85, 'unit' => 'Liter'],
                                    ['part_no' => 'OIL-TO4-SAE50', 'part_name' => 'Cat TDTO SAE 50 Final Drive Oil', 'qty' => 50, 'unit' => 'Liter'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'ADT-745-01',
                'wo_number' => 'WO-2608-0022',
                'date' => '2026-08-21',
                'breakdown_time' => '2026-08-21 09:00:00',
                'ready_time' => '2026-08-21 17:30:00',
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'ready',
                'current_hm' => 2890.0,
                'job_title' => 'Penyetelan Center Articulation Joint & Greasing Universal Joint ADT',
                'root_cause' => 'Bushing center hitch mulai aus akibat torsi tekuk jalan berlumpur',
                'action_taken' => 'Penyetelan preload tapered roller bearing center joint & greasing',
                'tasks' => [
                    [
                        'title' => 'Center Oscillation Joint Ada Play Berlebih Saat Belok',
                        'component' => 'Steering & Articulation System',
                        'breakdown_at' => '2026-08-21 09:00:00',
                        'ready_at' => '2026-08-21 17:30:00',
                        'subtasks' => [
                            [
                                'action' => 'Torquing center hitch locking nut & pasang seal v-ring baru',
                                'obstacle' => 'none',
                                'notes' => 'Gerakan artikulasi sasis kembali presisi',
                                'parts' => [
                                    ['part_no' => '328-9011', 'part_name' => 'V-Ring Seal Center Articulation', 'qty' => 2, 'unit' => 'Pcs'],
                                    ['part_no' => 'GREASE-MOLY', 'part_name' => 'High Load Mining Grease 5% Moly', 'qty' => 5, 'unit' => 'Kg'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'LV-DMX-01',
                'wo_number' => 'WO-2608-0023',
                'date' => '2026-08-22',
                'breakdown_time' => '2026-08-22 08:30:00',
                'ready_time' => '2026-08-22 13:00:00',
                'wo_type' => 'corrective',
                'priority' => 'medium',
                'unit_status' => 'ready',
                'current_hm' => 840.0,
                'job_title' => 'Ganti Pompa Transfer Solar 12V High-Flow pada Bak Service D-Max',
                'root_cause' => 'Motor brush dinamo transfer pump aus',
                'action_taken' => 'Ganti Pompa Solar Piusi 12V 80 LPM & pasang inline strainer',
                'tasks' => [
                    [
                        'title' => 'Fuel Transfer Pump Mati Saat Pengisian Solar di Pit',
                        'component' => 'Fuel & Injection System',
                        'breakdown_at' => '2026-08-22 08:30:00',
                        'ready_at' => '2026-08-22 13:00:00',
                        'subtasks' => [
                            [
                                'action' => 'Pemasangan Pompa DC 12V Baru & Uji Debit Aliran 80 L/min',
                                'obstacle' => 'none',
                                'notes' => 'Kecepatan pengisian solar normal tanpa kendala',
                                'parts' => [
                                    ['part_no' => 'PIUSI-BIPUMP-12V', 'part_name' => 'Piusi Bi-Pump 12V 80LPM Diesel Pump', 'qty' => 1, 'unit' => 'Pcs'],
                                    ['part_no' => 'HOSE-FUEL-1IN', 'part_name' => 'Rubber Fuel Hose 1 Inch x 4m', 'qty' => 1, 'unit' => 'Pcs'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            // Active Ongoing / In-Progress Event on Aug 23, 2026
            [
                'unit' => 'EX-349F-01',
                'wo_number' => 'WO-2608-0024',
                'date' => '2026-08-23',
                'breakdown_time' => '2026-08-23 06:45:00',
                'ready_time' => null, // ONGOING / IN PROGRESS
                'wo_type' => 'breakdown',
                'priority' => 'urgent',
                'unit_status' => 'in_progress',
                'current_hm' => 2980.0,
                'job_title' => 'Track Link Assembly Kanan Lepas / Lepas Pin Master Link di Pit Sump',
                'root_cause' => 'Master pin retainer ring terlepas akibat benturan batu keras',
                'action_taken' => 'Pemasangan track press portabel untuk memasang kembali pin master link',
                'tasks' => [
                    [
                        'title' => 'Rantai Track Kanan Terlepas dari Sprocket (Track De-railed)',
                        'component' => 'Undercarriage (Track, Roller, Idler, Sprocket)',
                        'breakdown_at' => '2026-08-23 06:45:00',
                        'ready_at' => null,
                        'subtasks' => [
                            [
                                'action' => 'Tarik track link kembali ke atas idler & pasang master pin baru',
                                'obstacle' => 'waiting_part',
                                'notes' => 'Menunggu master pin kit 349F sedang diambil dari Gudang SCM',
                                'parts' => [
                                    ['part_no' => 'CAT-MASTER-PIN', 'part_name' => 'Cat 349F Track Master Pin & Collar', 'qty' => 1, 'unit' => 'Set'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],

            [
                'unit' => 'OHT-777-01',
                'wo_number' => 'WO-2608-0025',
                'date' => '2026-08-23',
                'breakdown_time' => '2026-08-23 09:30:00',
                'ready_time' => null, // ONGOING / WAITING PART
                'wo_type' => 'corrective',
                'priority' => 'high',
                'unit_status' => 'breakdown',
                'current_hm' => 3950.0,
                'job_title' => 'Rembesan Oli Hoist Cylinder Kanan Saat Dump Body Mengangkat Beban 100T',
                'root_cause' => 'Wiper seal silinder hidrolik hoist robek',
                'action_taken' => 'Persiapan penggantian packing seal kit hoist cylinder',
                'tasks' => [
                    [
                        'title' => 'Kebocoran Oli Hidrolik di Stage 2 Hoist Telescopic Cylinder',
                        'component' => 'Dump Body & Hoist Hydraulic System',
                        'breakdown_at' => '2026-08-23 09:30:00',
                        'ready_at' => null,
                        'subtasks' => [
                            [
                                'action' => 'Ganjal safety prop dump body & lepas gland nut hoist cylinder',
                                'obstacle' => 'none',
                                'notes' => 'Safety lock terpasang aman',
                                'parts' => [
                                    ['part_no' => '232-4012', 'part_name' => 'Seal Kit Hoist Cylinder CAT 777F', 'qty' => 1, 'unit' => 'Set'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        DB::statement('DELETE FROM work_order_subtask_spareparts');
        DB::statement('DELETE FROM work_order_subtask_mechanics');
        DB::statement('DELETE FROM work_order_subtasks');
        DB::statement('DELETE FROM work_order_tasks');
        DB::statement('DELETE FROM work_order_mechanics');
        DB::statement('DELETE FROM work_orders');

        foreach ($workOrderData as $woIndex => $data) {
            $equipment = $findEquipment($data['unit']);
            if (! $equipment) {
                continue;
            }

            $breakdownAt = Carbon::parse($data['breakdown_time']);
            $readyAt = $data['ready_time'] ? Carbon::parse($data['ready_time']) : null;
            $downtime = 0;
            if ($readyAt) {
                $downtime = round($breakdownAt->diffInMinutes($readyAt) / 60, 2);
            }

            $primaryProblem = $data['tasks'][0]['title'] ?? $data['job_title'];
            $status = $data['unit_status'] === 'ready' ? 'completed' : ($data['unit_status'] === 'in_progress' ? 'in_progress' : 'open');

            $workOrder = WorkOrder::create([
                'wo_number' => $data['wo_number'],
                'wo_date' => Carbon::parse($data['date']),
                'wo_type' => $data['wo_type'],
                'priority' => $data['priority'],
                'status' => $status,
                'equipment_id' => $equipment->id,
                'site_id' => $equipment->site_id ?? $siteCentral->id,
                'current_hm' => $data['current_hm'],
                'current_km' => null,
                'requester_id' => $adminUser->id,
                'assigned_to_id' => $adminUser->id,
                'job_title' => $data['job_title'],
                'problem_description' => $primaryProblem,
                'action_taken' => $data['action_taken'],
                'root_cause' => $data['root_cause'],
                'scheduled_start_date' => $breakdownAt,
                'scheduled_end_date' => $readyAt ?: $breakdownAt->copy()->addHours(8),
                'actual_start_time' => $breakdownAt,
                'actual_end_time' => $readyAt,
                'breakdown_at' => $breakdownAt,
                'ready_at' => $readyAt,
                'downtime_hours' => $downtime,
                'unit_status' => $data['unit_status'],
                'is_opportunity' => false,
                'created_by' => $adminUser->id,
                'updated_by' => $adminUser->id,
                'created_at' => $breakdownAt,
                'updated_at' => $readyAt ?: $breakdownAt,
            ]);

            foreach ($data['tasks'] as $tIndex => $tData) {
                $tBreakdown = ! empty($tData['breakdown_at']) ? Carbon::parse($tData['breakdown_at']) : $breakdownAt;
                $tReady = ! empty($tData['ready_at']) ? Carbon::parse($tData['ready_at']) : $readyAt;
                $tDowntime = 0;
                if ($tReady && $tBreakdown) {
                    $tDowntime = round($tBreakdown->diffInMinutes($tReady) / 60, 2);
                }

                $reffCompId = $findComponentId($tData['component'] ?? '');

                $task = WorkOrderTask::create([
                    'work_order_id' => $workOrder->id,
                    'problem_title' => $tData['title'],
                    'component' => $tData['component'] ?? null,
                    'reff_component_id' => $reffCompId,
                    'is_primary' => $tIndex === 0,
                    'task_order' => $tIndex + 1,
                    'status' => $tReady ? 'completed' : 'open',
                    'breakdown_at' => $tBreakdown,
                    'ready_at' => $tReady,
                    'downtime_hours' => $tDowntime,
                    'created_at' => $tBreakdown,
                    'updated_at' => $tReady ?: $tBreakdown,
                ]);

                foreach ($tData['subtasks'] ?? [] as $sIndex => $sData) {
                    $subtask = WorkOrderSubtask::create([
                        'work_order_task_id' => $task->id,
                        'action_title' => $sData['action'],
                        'subtask_order' => $sIndex + 1,
                        'assigned_to_id' => $adminUser->id,
                        'status' => $tReady ? 'completed' : 'pending',
                        'labor_hours' => $tDowntime > 0 ? $tDowntime : 2.5,
                        'actual_start_time' => $tBreakdown,
                        'actual_end_time' => $tReady,
                        'breakdown_at' => $tBreakdown,
                        'ready_at' => $tReady,
                        'obstacle' => $sData['obstacle'] ?? 'none',
                        'obstacle_notes' => $sData['notes'] ?? null,
                        'created_at' => $tBreakdown,
                        'updated_at' => $tReady ?: $tBreakdown,
                    ]);

                    foreach ($sData['parts'] ?? [] as $part) {
                        WorkOrderSubtaskSparepart::create([
                            'work_order_subtask_id' => $subtask->id,
                            'part_number' => $part['part_no'],
                            'part_name' => $part['part_name'],
                            'quantity' => $part['qty'],
                            'unit' => $part['unit'] ?? 'Pcs',
                            'action_type' => 'replace',
                            'status' => 'installed',
                            'remarks' => 'Pemakaian langsung pengerjaan WO',
                        ]);
                    }
                }
            }
        }
    }
}
