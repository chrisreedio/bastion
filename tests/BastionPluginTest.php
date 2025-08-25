<?php

use ChrisReedIO\Bastion\BastionPlugin;
use ChrisReedIO\Bastion\Tests\TestUser;
use Filament\Panel;
use Illuminate\Support\Facades\Gate;

test('plugin can be instantiated', function () {
    $plugin = BastionPlugin::make();

    expect($plugin)->toBeInstanceOf(BastionPlugin::class);
});

test('plugin has correct id', function () {
    $plugin = BastionPlugin::make();

    expect($plugin->getId())->toBe('bastion');
});

test('super admin role can be set and retrieved', function () {
    $plugin = BastionPlugin::make();

    $plugin->superAdminRole('Developer');

    expect($plugin->getSuperAdminRole())->toBe('Developer');
});

test('super admin role can be set with closure', function () {
    $plugin = BastionPlugin::make();

    $plugin->superAdminRole(fn () => 'Admin');

    expect($plugin->getSuperAdminRole())->toBe('Admin');
});

test('super admin role can be set to null', function () {
    $plugin = BastionPlugin::make();

    $plugin->superAdminRole(null);

    expect($plugin->getSuperAdminRole())->toBeNull();
});

test('sso detection works when config is enabled', function () {
    config(['bastion.sso.enabled' => true]);

    $plugin = BastionPlugin::make();

    expect($plugin->getSsoEnabled())->toBeTrue();
});

test('sso detection works when config is disabled', function () {
    config(['bastion.sso.enabled' => false]);

    $plugin = BastionPlugin::make();

    expect($plugin->getSsoEnabled())->toBeFalse();
});

test('plugin boots and sets up gate for super admin', function () {
    $plugin = BastionPlugin::make();
    $plugin->superAdminRole('Developer');

    $panel = new Panel('test');

    // Create a test user with the Developer role
    $user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    // Create the role first
    $role = \Spatie\Permission\Models\Role::create([
        'name' => 'Developer',
        'guard_name' => 'web',
    ]);

    $user->assignRole('Developer');

    $plugin->boot($panel);

    // Test that the gate allows access for super admin
    $result = Gate::forUser($user)->check('any-ability');

    expect($result)->toBeTrue();
});

test('plugin boots without super admin role', function () {
    $plugin = BastionPlugin::make();
    $panel = new Panel('test');

    // Should not throw an exception
    $plugin->boot($panel);

    expect($plugin->getSuperAdminRole())->toBeNull();
});
