<?php

namespace App\Filament\Resources\EmployeeDetails;

use App\Filament\Resources\EmployeeDetails\Pages\CreateEmployeeDetail;
use App\Filament\Resources\EmployeeDetails\Pages\EditEmployeeDetail;
use App\Filament\Resources\EmployeeDetails\Pages\ListEmployeeDetails;
use App\Filament\Resources\EmployeeDetails\Schemas\EmployeeDetailForm;
use App\Filament\Resources\EmployeeDetails\Tables\EmployeeDetailsTable;
use App\Models\EmployeeDetail;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Actions;
use App\Filament\Imports\EmployeeDetailImporter;

class EmployeeDetailResource extends Resource
{
    protected static ?string $model = EmployeeDetail::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()?->hasRole('User');
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeDetailForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeDetailsTable::configure($table);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(EmployeeDetailImporter::class),
            Actions\CreateAction::make(),
        ];
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
            'index' => ListEmployeeDetails::route('/'),
            'create' => CreateEmployeeDetail::route('/create'),
            'edit' => EditEmployeeDetail::route('/{record}/edit'),
        ];
    }
}
