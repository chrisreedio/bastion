<?php

use ChrisReedIO\Bastion\BastionServiceProvider;

test('service provider gets migrations correctly', function () {
    $provider = new BastionServiceProvider(app());

    // Get the migrations that should be included
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getMigrations');
    $method->setAccessible(true);
    $migrations = $method->invoke($provider);

    expect($migrations)->toContain('modify_permissions_table_add_resource_column');
    expect($migrations)->toContain('modify_permissions_table_add_display_name_column');
    expect($migrations)->toContain('modify_roles_table_add_sso_group_column');
});

test('service provider gets asset package name correctly', function () {
    $provider = new BastionServiceProvider(app());

    // Get the asset package name
    $reflection = new ReflectionClass($provider);
    $method = $reflection->getMethod('getAssetPackageName');
    $method->setAccessible(true);
    $packageName = $method->invoke($provider);

    expect($packageName)->toBe('chrisreedio/bastion');
});
