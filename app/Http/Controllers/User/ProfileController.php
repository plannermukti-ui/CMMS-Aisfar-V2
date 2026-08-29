<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $workOrders = WorkOrder::with(['equipment.reffEquip', 'site', 'tasks.subtasks'])
            ->where(function ($q) use ($userId) {
                $q->where('requester_id', $userId)
                    ->orWhere('assigned_to_id', $userId)
                    ->orWhereHas('subtasks', function ($st) use ($userId) {
                        $st->where('assigned_to_id', $userId)
                            ->orWhereHas('mechanics', function ($m) use ($userId) {
                                $m->where('users.id', $userId);
                            });
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('user.profile', compact('workOrders'));
    }
}
