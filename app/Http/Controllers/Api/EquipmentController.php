<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Equipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Equipment::class);

        $equipments = Equipment::with(['createdBy', 'updatedBy'])->latest()->paginate(10);

        return response()->json(['data' => $equipments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEquipmentRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['updated_by'] = Auth::id();

            $equipment = Equipment::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Equipment created successfully',
                'data' => $equipment,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to create equipment', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipment $equipment)
    {
        $this->authorize('view', $equipment);

        $equipment->load(['createdBy', 'updatedBy']);

        return response()->json(['data' => $equipment]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEquipmentRequest $request, Equipment $equipment)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['updated_by'] = Auth::id();

            $equipment->update($data);

            DB::commit();

            return response()->json([
                'message' => 'Equipment updated successfully',
                'data' => $equipment->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to update equipment', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipment $equipment)
    {
        $this->authorize('delete', $equipment);

        DB::beginTransaction();
        try {
            $equipment->delete();
            DB::commit();

            return response()->json(['message' => 'Equipment deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['message' => 'Failed to delete equipment', 'error' => $e->getMessage()], 500);
        }
    }
}
