<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use App\Models\Site;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Equipments', Equipment::count())
                ->description('Active fleet')
                ->descriptionIcon('heroicon-m-truck')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Total Users', User::count())
                ->description('Registered accounts')
                ->descriptionIcon('heroicon-m-users')
                ->chart([1, 4, 3, 5, 2, 8, 10])
                ->color('primary'),
            Stat::make('Total Sites', Site::count())
                ->description('Active locations')
                ->descriptionIcon('heroicon-m-map-pin')
                ->chart([2, 2, 3, 4, 5, 5, 5])
                ->color('warning'),
        ];
    }
}
