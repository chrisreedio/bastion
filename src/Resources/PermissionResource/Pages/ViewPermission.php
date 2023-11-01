<?php

namespace ChrisReedIO\Bastion\Resources\PermissionResource\Pages;

use ChrisReedIO\Bastion\Resources\PermissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPermission extends ViewRecord
{
	protected static string $resource = PermissionResource::class;

	public function getHeaderActions(): array
	{
		return [
			EditAction::make(),
		];
	}
}
