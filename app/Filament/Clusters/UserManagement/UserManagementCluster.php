<?php

namespace App\Filament\Clusters\UserManagement;

use Filament\Clusters\Cluster;

class UserManagementCluster extends Cluster
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Roles & Permissions';

    protected static ?string $clusterBreadcrumb = 'Roles & Permissions';
}
