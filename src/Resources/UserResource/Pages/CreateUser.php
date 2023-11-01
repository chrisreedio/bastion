<?php

namespace ChrisReedIO\Bastion\Resources\UserResource\Pages;

use ChrisReedIO\Bastion\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
