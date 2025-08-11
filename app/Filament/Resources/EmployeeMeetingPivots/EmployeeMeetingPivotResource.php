<?php

namespace App\Filament\Resources\EmployeeMeetingPivots;

use App\Filament\Resources\EmployeeMeetingPivots\Pages\CreateEmployeeMeetingPivot;
use App\Filament\Resources\EmployeeMeetingPivots\Pages\EditEmployeeMeetingPivot;
use App\Filament\Resources\EmployeeMeetingPivots\Pages\ListEmployeeMeetingPivots;
use App\Filament\Resources\EmployeeMeetingPivots\Schemas\EmployeeMeetingPivotForm;
use App\Filament\Resources\EmployeeMeetingPivots\Tables\EmployeeMeetingPivotsTable;
use App\Models\EmployeeMeetingPivot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmployeeMeetingPivotResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return false; // Disable navigation for this resource
    }

    protected static ?string $model = EmployeeMeetingPivot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EmployeeMeetingPivotForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeMeetingPivotsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeMeetingPivots::route('/'),
            'create' => CreateEmployeeMeetingPivot::route('/create'),
            'edit' => EditEmployeeMeetingPivot::route('/{record}/edit'),
        ];
    }
}
