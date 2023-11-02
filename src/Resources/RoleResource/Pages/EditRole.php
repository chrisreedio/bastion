<?php

namespace ChrisReedIO\Bastion\Resources\RoleResource\Pages;

use ChrisReedIO\Bastion\Resources\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
