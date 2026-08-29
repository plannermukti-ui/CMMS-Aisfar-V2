<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Role::class);

        $roles = Role::with('permissions')->get();

        return response()->json([
            'data' => $roles,
        ]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $permissions = $data['permissions'] ?? null;
            unset($data['permissions']);

            $role = Role::create($data);

            if ($permissions !== null) {
                $role->permissions()->sync($permissions);
            }

            return $role->load('permissions');
        });

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role,
        ], 201);
    }

    public function show(Role $role): JsonResponse
    {
        Gate::authorize('view', $role);

        return response()->json([
            'data' => $role->load('permissions'),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = DB::transaction(function () use ($request, $role) {
            $data = $request->validated();
            $permissions = $data['permissions'] ?? null;
            unset($data['permissions']);

            $role->update($data);

            if ($permissions !== null) {
                $role->permissions()->sync($permissions);
            }

            return $role->load('permissions');
        });

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role,
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        Gate::authorize('delete', $role);

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }
}
