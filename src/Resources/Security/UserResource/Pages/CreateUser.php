<?php

namespace ChrisReedIO\Bastion\Resources\Security\UserResource\Pages;

use ChrisReedIO\Bastion\Resources\Security\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
