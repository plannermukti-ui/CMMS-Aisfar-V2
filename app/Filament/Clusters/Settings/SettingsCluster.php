<?php

namespace App\Filament\Clusters\Settings;

use Filament\Clusters\Cluster;

class SettingsCluster extends Cluster
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $navigationLabel = 'Pengaturan (Settings)';

    protected static \UnitEnum|string|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 100;
}
