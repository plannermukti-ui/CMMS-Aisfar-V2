<?php

namespace App\Filament\Pages;

use App\Models\ActivityLog;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\WithPagination;

class ActivityLogPage extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.activity-log-page';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $navigationLabel = 'Activity Log';

    protected static ?string $title = 'Activity Log Sistem';

    protected static ?int $navigationSort = 99;

    protected static \UnitEnum|string|null $navigationGroup = 'System';

    public string $search = '';

    public string $filterModule = 'all';

    public string $filterAction = 'all';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasPermission('view activity-log') ?? false;
    }

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

    public function getViewData(): array
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

        return [
            'logs' => $query->paginate(25),
            'modules' => ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module'),
            'actions' => ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action'),
            'stats' => [
                'total' => ActivityLog::count(),
                'today' => ActivityLog::whereDate('created_at', today())->count(),
                'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'unique_users' => ActivityLog::distinct('user_id')->count('user_id'),
            ],
        ];
    }
}
