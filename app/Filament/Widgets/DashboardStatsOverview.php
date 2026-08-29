<?php

namespace App\Filament\Widgets;

use App\Models\ReffUser;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Karyawan Aktif', User::where('status', 'active')->count())
                ->description('Jumlah pengguna yang aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Departemen', ReffUser::where('type', 'department')->count())
                ->description('Struktur divisi terdaftar')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),

            Stat::make('Total Jabatan', ReffUser::where('type', 'position')->count())
                ->description('Posisi tersedia')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),
        ];
    }
}
