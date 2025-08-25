<?php

namespace ChrisReedIO\Bastion\Resources\PermissionResource\Pages;

use ChrisReedIO\Bastion\Bastion;
use ChrisReedIO\Bastion\Resources\PermissionResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label('Sync')
                ->action(function () {
                    Bastion::sync();
                }),
            CreateAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        $roleModel = config('permission.models.role');

        return [
            Actions\BulkAction::make('Attach Role')
                ->action(function (Collection $records, array $data): void {
                    /** @var Permission $record */
                    foreach ($records as $record) {
                        $record->roles()->sync($data['role']);
                        $record->save();
                    }
                })
                ->schema([
                    Select::make('role')
                        ->label(__('bastion::messages.field.role'))
                        ->options($roleModel::query()->pluck('name', 'id'))
                        ->required(),
                ])->deselectRecordsAfterCompletion(),
        ];
    }
}
