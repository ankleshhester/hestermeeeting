<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;

abstract class BaseTableWidget extends TableWidget
{
    protected function getDefaultTableSortColumn(): ?string
    {
        // Prevents default "ORDER BY id ASC"
        return null;
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return null;
    }
}
