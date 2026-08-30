<?php

namespace Tests\Feature;

use App\Livewire\Scm\DoPage;
use App\Livewire\Scm\GrPage;
use App\Livewire\Scm\PoPage;
use App\Models\Part;
use App\Models\Permission;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Role;
use App\Models\Site;
use App\Models\User;
use App\Models\Vendor;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class ScmPartialFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Vendor $vendor;

    private Site $site;

    private Part $partA;

    private Part $partB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        $role = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        $role->permissions()->sync(Permission::all());

        $this->user = User::create([
            'username' => 'scmofficer',
            'full_name' => 'SCM Officer',
            'email' => 'scm@test.com',
            'password' => Hash::make('password'),
        ]);
        $this->user->roles()->attach($role);

        $this->vendor = Vendor::create([
            'name' => 'PT Supplier Utama',
            'code' => 'VEND-001',
            'phone' => '08123456789',
        ]);

        $this->site = Site::create([
            'site_code' => 'SITE-01',
            'site_name' => 'Site Central',
        ]);

        $this->partA = Part::create([
            'part_number' => 'FLT-001',
            'name' => 'Oil Filter',
            'uom' => 'Pcs',
            'stock_on_hand' => 0,
            'standard_cost' => 150000,
            'is_active' => true,
        ]);

        $this->partB = Part::create([
            'part_number' => 'GSK-002',
            'name' => 'Cylinder Gasket',
            'uom' => 'Pcs',
            'stock_on_hand' => 0,
            'standard_cost' => 250000,
            'is_active' => true,
        ]);
    }

    public function test_partial_delivery_order_creation_and_po_status(): void
    {
        $po = PurchaseOrder::create([
            'vendor_id' => $this->vendor->id,
            'status' => 'approved',
            'po_date' => now()->toDateString(),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'part_id' => $this->partA->id,
            'part_number' => $this->partA->part_number,
            'part_name' => $this->partA->name,
            'quantity' => 10,
            'uom' => 'Pcs',
            'unit_price' => 150000,
            'subtotal' => 1500000,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'part_id' => $this->partB->id,
            'part_number' => $this->partB->part_number,
            'part_name' => $this->partB->name,
            'quantity' => 20,
            'uom' => 'Pcs',
            'unit_price' => 250000,
            'subtotal' => 5000000,
        ]);

        $this->assertTrue($po->hasUnshippedItems());

        // Step 1: Create partial DO 1 (Part A: 4 pcs, Part B: 10 pcs) via PoPage
        Livewire::actingAs($this->user)
            ->test(PoPage::class)
            ->call('openGenerateDoModal', $po->id)
            ->set('destination_site_id', $this->site->id)
            ->set('do_items.0.qty_to_ship', 4)
            ->set('do_items.1.qty_to_ship', 10)
            ->call('generateDeliveryOrder')
            ->assertHasNoErrors();

        $po->refresh();
        $this->assertEquals('partially_shipped', $po->status);
        $this->assertTrue($po->hasUnshippedItems());
        $this->assertEquals(4.0, $po->getItemShippedQuantity($this->partA->id));
        $this->assertEquals(10.0, $po->getItemShippedQuantity($this->partB->id));

        // Step 2: Create remaining DO 2 (Part A: 6 pcs, Part B: 10 pcs) via DoPage
        Livewire::actingAs($this->user)
            ->test(DoPage::class)
            ->call('openCreateModal', $po->id)
            ->set('destination_site_id', $this->site->id)
            ->set('do_items.0.qty_to_ship', 6)
            ->set('do_items.1.qty_to_ship', 10)
            ->call('saveDeliveryOrder')
            ->assertHasNoErrors();

        $po->refresh();
        $this->assertEquals('do_created', $po->status);
        $this->assertFalse($po->hasUnshippedItems());
        $this->assertEquals(10.0, $po->getItemShippedQuantity($this->partA->id));
        $this->assertEquals(20.0, $po->getItemShippedQuantity($this->partB->id));
    }

    public function test_partial_goods_receipt_and_status_tracking(): void
    {
        $po = PurchaseOrder::create([
            'vendor_id' => $this->vendor->id,
            'status' => 'approved',
            'po_date' => now()->toDateString(),
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'part_id' => $this->partA->id,
            'part_number' => $this->partA->part_number,
            'part_name' => $this->partA->name,
            'quantity' => 10,
            'uom' => 'Pcs',
            'unit_price' => 150000,
            'subtotal' => 1500000,
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'part_id' => $this->partB->id,
            'part_number' => $this->partB->part_number,
            'part_name' => $this->partB->name,
            'quantity' => 20,
            'uom' => 'Pcs',
            'unit_price' => 250000,
            'subtotal' => 5000000,
        ]);

        // Ship DO #1: Part A: 4 pcs, Part B: 10 pcs
        Livewire::actingAs($this->user)
            ->test(PoPage::class)
            ->call('openGenerateDoModal', $po->id)
            ->set('destination_site_id', $this->site->id)
            ->set('do_items.0.qty_to_ship', 4)
            ->set('do_items.1.qty_to_ship', 10)
            ->call('generateDeliveryOrder');

        $po->refresh();
        $do1 = $po->deliveryOrders()->first();

        // Ship DO #2: Part A: 6 pcs, Part B: 10 pcs
        Livewire::actingAs($this->user)
            ->test(PoPage::class)
            ->call('openGenerateDoModal', $po->id)
            ->set('destination_site_id', $this->site->id)
            ->set('do_items.0.qty_to_ship', 6)
            ->set('do_items.1.qty_to_ship', 10)
            ->call('generateDeliveryOrder');

        $po->refresh();
        $do2 = $po->deliveryOrders()->orderBy('created_at', 'desc')->first();

        // Step 1: GR #1 for DO #1 with partial quantities (Part A: 4, Part B: 7)
        Livewire::actingAs($this->user)
            ->test(GrPage::class)
            ->call('loadFromDo', $do1->id)
            ->set('items.0.qty_received', 4)
            ->set('items.1.qty_received', 7)
            ->call('saveGoodsReceipt')
            ->assertHasNoErrors();

        $do1->refresh();
        $po->refresh();
        $this->assertEquals('partially_received', $do1->status);
        $this->assertEquals('partially_received', $po->status);
        $this->assertTrue($do1->hasUnreceivedItems());

        $gr1 = $do1->goodsReceipts()->first();
        $this->assertEquals('partial', $gr1->status);

        // Check stock
        $this->partA->refresh();
        $this->partB->refresh();
        $this->assertEquals(4.0, (float) $this->partA->stock_on_hand);
        $this->assertEquals(7.0, (float) $this->partB->stock_on_hand);

        // Step 2: GR #2 for DO #1 remaining quantity (Part B: 3)
        Livewire::actingAs($this->user)
            ->test(GrPage::class)
            ->call('loadFromDo', $do1->id)
            ->set('items.0.qty_received', 0)
            ->set('items.1.qty_received', 3)
            ->call('saveGoodsReceipt')
            ->assertHasNoErrors();

        $do1->refresh();
        $po->refresh();
        $this->assertEquals('received', $do1->status);
        $this->assertEquals('partially_received', $po->status); // PO still partially received because DO2 is not received yet

        // Step 3: GR #3 for DO #2 fully (Part A: 6, Part B: 10)
        Livewire::actingAs($this->user)
            ->test(GrPage::class)
            ->call('loadFromDo', $do2->id)
            ->set('items.0.qty_received', 6)
            ->set('items.1.qty_received', 10)
            ->call('saveGoodsReceipt')
            ->assertHasNoErrors();

        $do2->refresh();
        $po->refresh();
        $this->assertEquals('received', $do2->status);
        $this->assertEquals('received', $po->status); // 100% of PO received!

        $this->partA->refresh();
        $this->partB->refresh();
        $this->assertEquals(10.0, (float) $this->partA->stock_on_hand);
        $this->assertEquals(20.0, (float) $this->partB->stock_on_hand);
    }
}
