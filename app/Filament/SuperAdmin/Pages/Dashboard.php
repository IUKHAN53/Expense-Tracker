<?php

namespace App\Filament\SuperAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use UnitEnum;

/**
 * The /superadmin landing page. Unlike the per-account Dashboard it carries no
 * month filter — its widgets report on the whole platform across all tenants.
 * Widgets are auto-discovered from app/Filament/SuperAdmin/Widgets.
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Platform overview';

    protected static string|UnitEnum|null $navigationGroup = null;

    public function getColumns(): int|array
    {
        return 2;
    }
}
