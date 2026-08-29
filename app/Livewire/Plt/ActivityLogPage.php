<?php

namespace App\Livewire\Plt;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('Activity Log - CMMS')]
class ActivityLogPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterModule = 'all';

    public string $filterAction = 'all';

    public string $filterUser = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterModule(): void
    {
        $this->resetPage();
    }

    public function updatedFilterAction(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ActivityLog::with('user')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('module', 'like', "%{$this->search}%")
                    ->orWhere('action', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('full_name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterModule !== 'all', fn ($q) => $q->where('module', $this->filterModule))
            ->when($this->filterAction !== 'all', fn ($q) => $q->where('action', $this->filterAction))
            ->orderByDesc('created_at');

        $logs = $query->paginate(25);

        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'unique_users' => ActivityLog::distinct('user_id')->count('user_id'),
        ];

        return view('livewire.plt.activity-log-page', compact('logs', 'modules', 'actions', 'stats'));
    }
}
