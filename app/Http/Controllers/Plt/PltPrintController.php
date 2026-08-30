<?php

namespace App\Http\Controllers\Plt;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentHm;
use App\Models\PlantCcr;
use App\Models\PlantComponent;
use App\Models\PlantFar;
use App\Models\PlantOsr;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PltPrintController extends Controller
{
    public function printWorkOrder(string $id): View
    {
        $workOrder = WorkOrder::with([
            'equipment',
            'site',
            'requester',
            'assignedTo',
            'approvedBy',
            'tasks.subtasks.spareparts',
            'tasks.subtasks.mechanics',
        ])->findOrFail($id);

        return view('print.plt.workorder', compact('workOrder'));
    }

    public function printCcr(string $id): View
    {
        $ccr = PlantCcr::with(['equipment', 'component', 'inspector', 'workOrder'])->findOrFail($id);

        return view('print.plt.ccr', compact('ccr'));
    }

    public function printFar(string $id): View
    {
        $far = PlantFar::with(['equipment', 'component', 'investigator', 'workOrder'])->findOrFail($id);

        return view('print.plt.far', compact('far'));
    }

    public function printOsr(string $id): View
    {
        $osr = PlantOsr::with(['equipment', 'component', 'vendor', 'workOrder', 'creator'])->findOrFail($id);

        return view('print.plt.osr', compact('osr'));
    }

    public function printComponent(string $id): View
    {
        $component = PlantComponent::with([
            'equipment',
            'movements.equipment',
            'conditionReports',
            'outsideRepairs.vendor',
            'failureReports',
        ])->findOrFail($id);

        return view('print.plt.component', compact('component'));
    }

    public function printHmUpdate(Request $request): View
    {
        $equipments = Equipment::with(['site', 'latestHmLog.creator'])
            ->when($request->search, function ($q, $search) {
                $term = '%'.strtolower(trim($search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(unit) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(engine_model) LIKE ?', [$term]);
                });
            })
            ->when($request->site_id, fn ($q, $site) => $q->where('site_id', $site))
            ->orderBy('unit')
            ->get();

        $recentLogs = EquipmentHm::with(['equipment', 'creator'])
            ->orderBy('date', 'desc')
            ->limit(50)
            ->get();

        return view('print.plt.hm-update', compact('equipments', 'recentLogs'));
    }
}
