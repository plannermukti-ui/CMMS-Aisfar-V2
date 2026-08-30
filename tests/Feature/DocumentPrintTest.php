<?php

namespace Tests\Feature;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Equipment;
use App\Models\EquipmentHm;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\MaterialOrder;
use App\Models\MaterialOrderItem;
use App\Models\Part;
use App\Models\PlantCcr;
use App\Models\PlantComponent;
use App\Models\PlantFar;
use App\Models\PlantOsr;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\RfqQuotation;
use App\Models\RfqQuotationItem;
use App\Models\Site;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\User;
use App\Models\Vendor;
use App\Models\WorkOrder;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DocumentPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $generalSettings = app(GeneralSettings::class);
        $generalSettings->site_name = 'PT MULTI ARTHA MINERAL';
        $generalSettings->company_address = 'Jl. Tambang Raya No. 10, Site Bontang, Kalimantan Timur | Telp. 021-5551234 | info@multiartha.co.id';
        $generalSettings->save();
    }

    public function test_print_routes_render_documents_with_company_letterhead(): void
    {
        $user = User::factory()->create([
            'username' => 'printuser',
            'full_name' => 'Print User',
            'email' => 'print@example.com',
            'password' => Hash::make('password'),
            'allowed_modules' => ['scm', 'plt'],
        ]);

        $site = Site::create([
            'site_code' => 'SITE-001',
            'site_name' => 'Site Bontang',
        ]);

        $vendor = Vendor::create([
            'name' => 'PT Supplier Utama',
            'code' => 'SUP-001',
            'phone' => '08123456789',
            'address' => 'Jl. Supplier No. 1',
            'npwp' => '01.234.567.8-901.234',
        ]);

        $part = Part::create([
            'part_number' => 'P-1001',
            'name' => 'Engine Oil Filter',
            'uom' => 'Pcs',
            'stock_on_hand' => 5,
            'min_stock' => 2,
            'standard_cost' => 150000,
            'is_active' => true,
        ]);

        $equipment = Equipment::create([
            'unit' => 'PC200-01',
            'no' => 'EQ-1001',
            'status' => 'Active',
            'site_id' => $site->id,
        ]);

        EquipmentHm::create([
            'equipment_id' => $equipment->id,
            'date' => '2025-01-10',
            'hm_value' => 1200,
            'source' => 'manual',
            'created_by' => $user->id,
        ]);

        $po = PurchaseOrder::create([
            'vendor_id' => $vendor->id,
            'status' => 'approved',
            'po_date' => '2025-01-15',
            'subtotal_dpp' => 1500000,
            'discount_amount' => 0,
            'ppn_percentage' => 11,
            'ppn_amount' => 165000,
            'shipping_cost' => 50000,
            'grand_total' => 1715000,
            'payment_terms' => 'Net 30 Days',
            'approved_by' => $user->id,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'part_id' => $part->id,
            'part_number' => $part->part_number,
            'part_name' => $part->name,
            'quantity' => 10,
            'uom' => 'Pcs',
            'unit_price' => 150000,
            'discount_amount' => 0,
            'subtotal' => 1500000,
        ]);

        $do = DeliveryOrder::create([
            'purchase_order_id' => $po->id,
            'do_date' => '2025-01-20',
            'destination_site_id' => $site->id,
            'destination_location_name' => 'Warehouse Site Bontang',
            'origin_location' => 'Supplier Warehouse',
            'expedition_name' => 'CV Mandiri Express',
            'vehicle_plate_number' => 'BK 9099 AA',
            'tracking_number' => 'AWB-123456',
            'status' => 'in_transit',
            'created_by' => $user->id,
        ]);

        DeliveryOrderItem::create([
            'delivery_order_id' => $do->id,
            'part_id' => $part->id,
            'part_number' => $part->part_number,
            'part_name' => $part->name,
            'qty_shipped' => 10,
            'uom' => 'Pcs',
        ]);

        $gr = GoodsReceipt::create([
            'purchase_order_id' => $po->id,
            'delivery_order_id' => $do->id,
            'site_id' => $site->id,
            'gr_date' => '2025-01-25',
            'status' => 'completed',
            'received_by_id' => $user->id,
            'notes' => 'Barang baru masuk gudang',
        ]);

        GoodsReceiptItem::create([
            'goods_receipt_id' => $gr->id,
            'part_id' => $part->id,
            'part_number' => $part->part_number,
            'part_name' => $part->name,
            'qty_received' => 10,
            'unit_price' => 150000,
        ]);

        $mol = MaterialOrder::create([
            'work_order_id' => null,
            'requester_id' => $user->id,
            'status' => 'approved',
            'approved_by' => $user->id,
            'notes' => 'Material untuk unit',
            'mol_date' => '2025-02-01',
        ]);

        MaterialOrderItem::create([
            'material_order_id' => $mol->id,
            'part_id' => $part->id,
            'part_number' => $part->part_number,
            'part_name' => $part->name,
            'qty_requested' => 2,
            'qty_issued' => 2,
        ]);

        $pr = PurchaseRequest::create([
            'pr_date' => '2025-02-05',
            'material_order_id' => $mol->id,
            'requester_id' => $user->id,
            'priority' => 'high',
            'required_date' => '2025-02-10',
            'status' => 'approved',
            'approved_by' => $user->id,
            'remarks' => 'Untuk kebutuhan site',
        ]);

        PurchaseRequestItem::create([
            'purchase_request_id' => $pr->id,
            'part_id' => $part->id,
            'part_number' => $part->part_number,
            'part_name' => $part->name,
            'quantity' => 5,
            'uom' => 'Pcs',
        ]);

        $rfq = RfqQuotation::create([
            'purchase_request_id' => $pr->id,
            'vendor_id' => $vendor->id,
            'quotation_number' => 'QTN-01',
            'subtotal_dpp' => 750000,
            'discount_amount' => 0,
            'ppn_percentage' => 11,
            'ppn_amount' => 82500,
            'shipping_cost' => 25000,
            'grand_total' => 857500,
            'delivery_lead_time_days' => 7,
            'is_selected' => true,
            'notes' => 'Harga terbaik',
        ]);

        RfqQuotationItem::create([
            'rfq_quotation_id' => $rfq->id,
            'purchase_request_item_id' => $pr->items()->first()->id,
            'qty_ready' => 5,
            'unit_price' => 150000,
            'discount_amount' => 0,
            'subtotal' => 750000,
            'is_selected' => true,
        ]);

        $opname = StockOpname::create([
            'opname_date' => '2025-02-15',
            'site_id' => $site->id,
            'conducted_by_id' => $user->id,
            'status' => 'approved',
            'approved_by_id' => $user->id,
            'notes' => 'Stock opname bulanan',
            'discrepancy_reason' => 'Selisih hasil hitung',
            'total_variance_qty' => 1,
            'total_variance_value' => 150000,
        ]);

        StockOpnameItem::create([
            'stock_opname_id' => $opname->id,
            'part_id' => $part->id,
            'part_number' => $part->part_number,
            'part_name' => $part->name,
            'uom' => 'Pcs',
            'rack_location' => 'A-01',
            'system_stock' => 5,
            'physical_stock' => 4,
            'difference_qty' => -1,
            'unit_cost' => 150000,
            'variance_cost' => -150000,
            'discrepancy_notes' => 'Satu unit rusak',
        ]);

        $workOrder = WorkOrder::create([
            'wo_date' => '2025-03-01',
            'wo_type' => 'corrective',
            'priority' => 'high',
            'status' => 'open',
            'equipment_id' => $equipment->id,
            'site_id' => $site->id,
            'requester_id' => $user->id,
            'job_title' => 'Unit breakdown',
            'problem_description' => 'Komponen mengalami masalah',
            'created_by' => $user->id,
        ]);

        $component = PlantComponent::create([
            'name' => 'Turbo Charger',
            'component_type' => 'engine',
            'serial_number' => 'CMP-001',
            'brand_model' => 'CAT 3306',
            'equipment_id' => $equipment->id,
            'position' => 'Engine Room',
            'status' => 'installed',
            'accumulated_hours' => 5200,
            'target_life_hours' => 10000,
            'installed_at_hm' => 1200,
            'installed_date' => '2025-01-01',
            'remarks' => 'Komponen utama',
            'created_by' => $user->id,
        ]);

        $ccr = PlantCcr::create([
            'ccr_date' => '2025-03-02',
            'equipment_id' => $equipment->id,
            'component_id' => $component->id,
            'component_name' => $component->name,
            'current_unit_hm' => 1300,
            'component_running_hours' => 5200,
            'wear_percentage' => 52,
            'physical_condition' => 'fair_wear',
            'leakage_status' => 'none',
            'noise_vibration_status' => 'normal',
            'oil_contamination_status' => 'clean',
            'findings_description' => 'Kondisi masih aman dan siap dipantau',
            'recommendation' => 'monitor_next_service',
            'estimated_remaining_hours' => 4800,
            'inspector_id' => $user->id,
            'status' => 'approved',
            'work_order_id' => $workOrder->id,
            'created_by' => $user->id,
        ]);

        $far = PlantFar::create([
            'incident_date' => '2025-03-05',
            'equipment_id' => $equipment->id,
            'component_id' => $component->id,
            'work_order_id' => $workOrder->id,
            'investigator_id' => $user->id,
            'unit_hm_at_failure' => 1400,
            'component_hm_at_failure' => 5300,
            'failure_type' => 'premature_failure',
            'failure_title' => 'Premature wear pada turbo charger',
            'problem_statement' => 'Kinerja menurun akibat keausan',
            'failure_symptoms' => 'Suara tidak normal dan performa turun',
            'root_cause_5why' => ['why1' => 'Kinerja menurun', 'why2' => 'Akses udara berkurang', 'why3' => 'Saringan kotor', 'why4' => 'Tidak ada jadwal penggantian', 'why5' => 'Belum ada SOP preventive'],
            'fishbone_factors' => ['man' => 'Operator kurang perhatian', 'machine' => 'Kotoran masuk', 'material' => 'Saringan kotor', 'method' => 'SOP belum berjalan', 'environment' => 'Debu tinggi'],
            'root_cause_summary' => 'Kebersihan saringan tidak terjaga',
            'direct_cause' => 'Debu masuk ke sistem intake',
            'corrective_actions' => 'Bersihkan sistem intake dan ganti filter',
            'preventive_actions' => 'Tambah inspeksi mingguan',
            'cost_impact_estimate' => 2500000,
            'downtime_hours_estimate' => 6,
            'status' => 'closed',
            'created_by' => $user->id,
        ]);

        $osr = PlantOsr::create([
            'order_date' => '2025-03-07',
            'equipment_id' => $equipment->id,
            'component_id' => $component->id,
            'work_order_id' => $workOrder->id,
            'vendor_id' => $vendor->id,
            'item_description' => 'Repair turbo charger',
            'scope_of_work' => 'Balancing and overhaul',
            'reason_for_outside' => 'Kapasitas workshop terbatas',
            'dispatch_date' => '2025-03-08',
            'estimated_completion_date' => '2025-03-16',
            'estimated_cost' => 4500000,
            'status' => 'testing_qc',
            'qc_passed' => true,
            'qc_notes' => 'Uji kualitas berhasil',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('scm.po.print', $po))
            ->assertOk()
            ->assertSee('PT MULTI ARTHA MINERAL')
            ->assertSee('PURCHASE ORDER');

        $this->actingAs($user)
            ->get(route('scm.do.print', $do))
            ->assertOk()
            ->assertSee('DELIVERY ORDER');

        $this->actingAs($user)
            ->get(route('scm.gr.print', $gr))
            ->assertOk()
            ->assertSee('GOODS RECEIPT');

        $this->actingAs($user)
            ->get(route('scm.mol.print', $mol))
            ->assertOk()
            ->assertSee('MATERIAL ORDER');

        $this->actingAs($user)
            ->get(route('scm.pr.print', $pr))
            ->assertOk()
            ->assertSee('PURCHASE REQUISITION');

        $this->actingAs($user)
            ->get(route('scm.rfq.print', $rfq))
            ->assertOk()
            ->assertSee('RFQ');

        $this->actingAs($user)
            ->get(route('scm.opname.print', $opname))
            ->assertOk()
            ->assertSee('STOCK OPNAME');

        $this->actingAs($user)
            ->get(route('scm.parts.print'))
            ->assertOk()
            ->assertSee('MASTER PARTS CATALOG');

        $this->actingAs($user)
            ->get(route('plt.workorder.print', $workOrder))
            ->assertOk()
            ->assertSee('WORK ORDER');

        $this->actingAs($user)
            ->get(route('plt.ccr.print', $ccr))
            ->assertOk()
            ->assertSee('CCR');

        $this->actingAs($user)
            ->get(route('plt.far.print', $far))
            ->assertOk()
            ->assertSee('FAR');

        $this->actingAs($user)
            ->get(route('plt.osr.print', $osr))
            ->assertOk()
            ->assertSee('OSR');

        $this->actingAs($user)
            ->get(route('plt.components.print', $component))
            ->assertOk()
            ->assertSee('COMPONENT');

        $this->actingAs($user)
            ->get(route('plt.hm-update.print'))
            ->assertOk()
            ->assertSee('HM UPDATE');
    }
}
