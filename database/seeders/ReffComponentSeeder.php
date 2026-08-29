<?php

namespace Database\Seeders;

use App\Models\ReffComponent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReffComponentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $components = [
            // General / All Units
            [
                'code' => 'CMP-ENG',
                'name' => 'Engine System',
                'category' => 'Powertrain',
                'equipment_types' => null, // Applicable to all
                'description' => 'Blok mesin, turbocharger, crankshaft, cylinder head, piston, & valve train.',
                'sort_order' => 1,
            ],
            [
                'code' => 'CMP-COL',
                'name' => 'Cooling & Radiator System',
                'category' => 'Mechanical',
                'equipment_types' => null,
                'description' => 'Radiator, aftercooler, water pump, thermostat, fan drive, & hoses.',
                'sort_order' => 2,
            ],
            [
                'code' => 'CMP-FUL',
                'name' => 'Fuel & Injection System',
                'category' => 'Mechanical',
                'equipment_types' => null,
                'description' => 'Fuel injection pump (FIP), common rail injector, fuel filter, & fuel tank.',
                'sort_order' => 3,
            ],
            [
                'code' => 'CMP-ELE',
                'name' => 'Electrical & Instrument System',
                'category' => 'Electrical',
                'equipment_types' => null,
                'description' => 'Alternator, starter motor, battery, ECM/controller, wiring harness, sensor & display gauge.',
                'sort_order' => 4,
            ],
            [
                'code' => 'CMP-CAB',
                'name' => 'Cabin & Air Conditioning (AC)',
                'category' => 'HVAC',
                'equipment_types' => null,
                'description' => 'Kompresor AC, evaporator, blower, kondensor, operator seat, wiper, & control panel.',
                'sort_order' => 5,
            ],
            [
                'code' => 'CMP-LUB',
                'name' => 'Lubrication & Auto-Greasing',
                'category' => 'Mechanical',
                'equipment_types' => null,
                'description' => 'Auto lube pump, distributor valve block, grease injectors, & piping hose.',
                'sort_order' => 6,
            ],
            [
                'code' => 'CMP-FRM',
                'name' => 'Chassis & Main Frame',
                'category' => 'Structure',
                'equipment_types' => null,
                'description' => 'Main chassis frame, bumper, counterweight, ROPS/FOPS structure, & walkway.',
                'sort_order' => 7,
            ],

            // Tracked Equipment Only (Excavator & Dozer)
            [
                'code' => 'CMP-UC',
                'name' => 'Undercarriage (Track, Roller, Idler, Sprocket)',
                'category' => 'Structure',
                'equipment_types' => ['Excavator', 'Dozer'],
                'description' => 'Track shoe, track link assembly, carrier roller, track roller, front idler, sprocket segment, & recoil spring tensioner.',
                'sort_order' => 10,
            ],
            [
                'code' => 'CMP-SWG',
                'name' => 'Swing Machinery & Center Joint',
                'category' => 'Mechanical',
                'equipment_types' => ['Excavator', 'Mobile Crane'],
                'description' => 'Swing motor, swing reduction gearbox, swing circle bearing, swing brake, & swivel center joint.',
                'sort_order' => 11,
            ],
            [
                'code' => 'CMP-ATT-BKT',
                'name' => 'Work Attachment (Boom, Arm, Bucket & GET)',
                'category' => 'Structure',
                'equipment_types' => ['Excavator', 'Wheel Loader'],
                'description' => 'Boom assembly, arm/stick, rock bucket, bucket teeth, adapter, side cutter, lip shroud, pin & bushing.',
                'sort_order' => 12,
            ],
            [
                'code' => 'CMP-ATT-BLD',
                'name' => 'Work Attachment (Blade & Ripper)',
                'category' => 'Structure',
                'equipment_types' => ['Dozer', 'Motor Grader'],
                'description' => 'Semi-U / Universal Blade, cutting edge, end bit, push arm, single/multi shank ripper, & ripper tip.',
                'sort_order' => 13,
            ],

            // Wheeled Equipment Only (OHT, ADT, Wheel Loader, Motor Grader, LV, Trucks)
            [
                'code' => 'CMP-WHL',
                'name' => 'Wheel, Rim & Tire System',
                'category' => 'Mechanical',
                'equipment_types' => [
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Wheel Loader',
                    'Motor Grader',
                    'Highway Dump Truck',
                    'Light Vehicle',
                    'Water Truck',
                    'Fuel Truck',
                    'Lube Service Truck',
                ],
                'description' => 'Ban (OTR / Radial Tire), wheel rim 5-piece, wheel nut/stud, tire valve, & bead seat band.',
                'sort_order' => 20,
            ],
            [
                'code' => 'CMP-SUS',
                'name' => 'Suspension System (Struts / Leaf Springs)',
                'category' => 'Mechanical',
                'equipment_types' => [
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Wheel Loader',
                    'Motor Grader',
                    'Highway Dump Truck',
                    'Light Vehicle',
                    'Water Truck',
                    'Fuel Truck',
                ],
                'description' => 'Front & rear suspension cylinder (nitrogen strut), leaf spring, stabilizer bar, torque rod, & A-frame.',
                'sort_order' => 21,
            ],
            [
                'code' => 'CMP-TRM',
                'name' => 'Transmission & Torque Converter',
                'category' => 'Powertrain',
                'equipment_types' => [
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Wheel Loader',
                    'Motor Grader',
                    'Dozer',
                    'Highway Dump Truck',
                    'Water Truck',
                    'Fuel Truck',
                ],
                'description' => 'Torque converter, planetary powershift transmission, transfer case, control valve, & transmission oil cooler.',
                'sort_order' => 22,
            ],
            [
                'code' => 'CMP-AXL',
                'name' => 'Drive Axle & Differential',
                'category' => 'Powertrain',
                'equipment_types' => [
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Wheel Loader',
                    'Motor Grader',
                    'Highway Dump Truck',
                    'Light Vehicle',
                    'Water Truck',
                    'Fuel Truck',
                ],
                'description' => 'Differential assembly, final drive planetary, axle shaft, drive shaft (propeller), & universal joint.',
                'sort_order' => 23,
            ],
            [
                'code' => 'CMP-BRK',
                'name' => 'Brake & Retarder System',
                'category' => 'Hydraulic',
                'equipment_types' => [
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Wheel Loader',
                    'Motor Grader',
                    'Highway Dump Truck',
                    'Light Vehicle',
                    'Water Truck',
                    'Fuel Truck',
                ],
                'description' => 'Oil-cooled multiple disc brake, ARC hydraulic retarder, service brake valve, parking brake, & brake accumulator.',
                'sort_order' => 24,
            ],
            [
                'code' => 'CMP-STR',
                'name' => 'Steering & Articulation System',
                'category' => 'Hydraulic',
                'equipment_types' => [
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Wheel Loader',
                    'Motor Grader',
                    'Highway Dump Truck',
                    'Light Vehicle',
                    'Water Truck',
                    'Fuel Truck',
                ],
                'description' => 'Steering cylinder, orbitrol metering pump, steering priority valve, tie rod, & center articulation joint.',
                'sort_order' => 25,
            ],
            [
                'code' => 'CMP-HST',
                'name' => 'Dump Body & Hoist Hydraulic System',
                'category' => 'Hydraulic',
                'equipment_types' => [
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Highway Dump Truck',
                ],
                'description' => 'Hoist hydraulic cylinder teleskopik, hoist control valve, dump body rock liner, & canopy protector.',
                'sort_order' => 26,
            ],

            // Hydraulic System (General for Plant Excavators, Dozers, Loaders, Graders)
            [
                'code' => 'CMP-HYD',
                'name' => 'Main Hydraulic & Pump System',
                'category' => 'Hydraulic',
                'equipment_types' => [
                    'Excavator',
                    'Dozer',
                    'Wheel Loader',
                    'Motor Grader',
                    'Off-Highway Dump Truck',
                    'Articulated Dump Truck',
                    'Mobile Crane',
                ],
                'description' => 'Main hydraulic pump (axial piston), main control valve (MCV), hydraulic tank, suction/return filter, & pilot system.',
                'sort_order' => 30,
            ],

            // Motor Grader Specific
            [
                'code' => 'CMP-GRD-BLD',
                'name' => 'Moldboard, Circle Drive & Drawbar',
                'category' => 'Structure',
                'equipment_types' => ['Motor Grader'],
                'description' => 'Circle gearbox, hydraulic motor, drawbar, moldboard blade shift cylinder, & tip cylinder.',
                'sort_order' => 35,
            ],

            // Support & Ancillary Equipment Specific
            [
                'code' => 'CMP-WT-CAN',
                'name' => 'Water Cannon & Pumping System',
                'category' => 'Mechanical',
                'equipment_types' => ['Water Truck'],
                'description' => 'High pressure water pump, remote hydraulic water cannon, gravity spray bar, & suction hose.',
                'sort_order' => 40,
            ],
            [
                'code' => 'CMP-FT-DSP',
                'name' => 'Fuel Dispenser & Metering System',
                'category' => 'Mechanical',
                'equipment_types' => ['Fuel Truck'],
                'description' => 'Positive displacement flow meter (TCS), high/low flow hose reel, auto shut-off nozzle, & emergency stop valve.',
                'sort_order' => 41,
            ],
            [
                'code' => 'CMP-CRN-BM',
                'name' => 'Telescopic Boom & Winch Hoist Crane',
                'category' => 'Structure',
                'equipment_types' => ['Mobile Crane'],
                'description' => 'Telescopic boom cylinder, main & auxiliary winch, wire rope, hook block, outrigger jacks, & load moment indicator (LMI).',
                'sort_order' => 42,
            ],
            [
                'code' => 'CMP-TL-MST',
                'name' => 'Lighting Mast & Floodlight System',
                'category' => 'Electrical',
                'equipment_types' => ['Tower Lamp'],
                'description' => 'Telescopic mast, hydraulic/hand winch, LED/Metal Halide lamp heads, ballast, & mast rotation lock.',
                'sort_order' => 43,
            ],
            [
                'code' => 'CMP-GEN-ALT',
                'name' => 'Alternator & Generator Power Head',
                'category' => 'Electrical',
                'equipment_types' => ['Generator Set', 'Tower Lamp'],
                'description' => 'Brushless alternator head, automatic voltage regulator (AVR), main circuit breaker (MCCB), & synchronizing panel.',
                'sort_order' => 44,
            ],
        ];

        DB::statement('DELETE FROM reff_components');

        foreach ($components as $item) {
            ReffComponent::create([
                'code' => $item['code'],
                'name' => $item['name'],
                'category' => $item['category'],
                'equipment_types' => $item['equipment_types'],
                'description' => $item['description'],
                'status' => 'Active',
                'sort_order' => $item['sort_order'],
            ]);
        }
    }
}
