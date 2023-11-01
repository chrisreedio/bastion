<?php

namespace ChrisReedIO\Bastion\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ChrisReedIO\Bastion\Bastion
 */
class Bastion extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ChrisReedIO\Bastion\Bastion::class;
    }
}
