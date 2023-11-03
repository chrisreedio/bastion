<?php

namespace ChrisReedIO\Bastion\Enums;

use Filament\Support\Contracts\HasLabel;

enum DefaultPermissions: string implements HasLabel
{
    case ViewAny = 'view_any';
    case View = 'view';
    case Create = 'create';
    case Update = 'update';
    case Delete = 'delete';
    case Restore = 'restore';
    case ForceDelete = 'force_delete';

    public function getLabel(): string
    {
        return match ($this) {
            self::ViewAny => 'View Any',
            self::View => 'View',
            self::Create => 'Create',
            self::Update => 'Update',
            self::Delete => 'Delete',
            self::Restore => 'Restore',
            self::ForceDelete => 'Force Delete',
        };
    }
}
