<?php

namespace ChrisReedIO\Bastion;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;

class BastionPlugin implements Plugin
{
    protected ?string $superAdminRole = null;

    public function getId(): string
    {
        return 'bastion';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            Resources\UserResource::class,
            Resources\RoleResource::class,
            Resources\PermissionResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        if ($this->superAdminRole) {
            Gate::before(function ($user, $ability) {
                return $user->hasRole($this->superAdminRole) ? true : null;
            });
        }
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

    public function superAdminRole(string|Closure $role = null): static
    {
        if ($role instanceof Closure) {
            $role = $role();
        }
        $this->superAdminRole = $role;

        return $this;
    }

    public function getSuperAdminRole(): ?string
    {
        return $this->superAdminRole;
    }
}
