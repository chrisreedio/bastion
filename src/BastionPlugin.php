<?php

namespace ChrisReedIO\Bastion;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;
use function config;
use function is_null;

class BastionPlugin implements Plugin
{
    protected ?string $superAdminRole = null;

    public function getId(): string
    {
        return 'bastion';
    }

    public function register(Panel $panel): void
    {
        // Register our resources
        $resources = config('bastion.resources');
        foreach ($resources as $name => $resource) {
            if (is_null($resource)) {
                continue;
            }
            $panel->resources([$resource]);
        }
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

    public function superAdminRole(string | Closure | null $role = null): static
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

    public function getSsoEnabled(): bool
    {
        return config('bastion.sso.enabled', false) || class_exists(\ChrisReedIO\Socialment\SocialmentPlugin::class, false);
    }
}
