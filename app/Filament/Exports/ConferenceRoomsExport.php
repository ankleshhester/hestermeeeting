<?php

namespace App\Filament\Exports;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Maatwebsite\Excel\Excel;

class ConferenceRoomsExport extends ExcelExport
{
    public static function make(string $name = 'export'): static
    {
        return parent::make($name)
            ->fromTable()
            ->withFilename('conference_rooms_' . now()->format('Y-m-d_H-i-s'))
            ->withWriterType(Excel::XLSX); // or Excel::CSV, Excel::ODS
    }
}
