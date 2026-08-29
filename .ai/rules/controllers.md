# Controllers Conventions

## API Controller Pattern

Semua API controller berada di `app/Http/Controllers/Api/` dan mengikuti pola ini:

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    public function index()
    {
        $this->authorize('view work-order');
        $items = WorkOrder::with(['createdBy'])->latest()->paginate(10);
        return response()->json(['data' => $items]);
    }

    public function store(StoreWorkOrderRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();
            $item = WorkOrder::create($data);
            DB::commit();
            return response()->json(['message' => 'Created', 'data' => $item], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed', 'error' => $e->getMessage()], 500);
        }
    }
}
```

## Wajib dalam Setiap Controller

- `$this->authorize(...)` di `index()`, `show()`, `destroy()`
- `DB::beginTransaction()` / `DB::commit()` / `DB::rollBack()` di `store()`, `update()`, `destroy()`
- `Auth::id()` untuk mengisi `created_by` / `updated_by`
- Gunakan `$request->validated()` — JANGAN `$request->all()`

## Tidak Ada Web Resource Controller

Tidak ada `app/Http/Controllers/EquipmentController.php` atau sejenisnya untuk web. Semua UI melalui Filament.
