<?php

namespace ChrisReedIO\Bastion;

use Filament\Contracts\Plugin;
use Filament\Panel;

class BastionPlugin implements Plugin
{
    public function getId(): string
    {
        return 'bastion';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            Resources\Security\UserResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
