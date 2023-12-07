<?php

// config for ChrisReedIO/Bastion
return [
    'models' => [
        'permission' => \Spatie\Permission\Models\Permission::class,
        'role' => \Spatie\Permission\Models\Role::class,
        'user' => '\App\Models\User',
    ],

    'permissions' => [
        'preload' => true,

    ],

    'default_guard' => 'web',
    'guards' => [
        // value => 'Custom Label'
        'web' => 'Web',
        'api' => 'API',
        // Your other custom guards here
    ],

    'sso' => [
        'enabled' => false,
    ],
];
