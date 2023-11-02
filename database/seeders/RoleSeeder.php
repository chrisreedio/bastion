<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $defaultGuard = config('bastion.default_guard');
        $roles = [
            [
                'name' => 'Developer',
                'guard_name' => $defaultGuard,
                'sso_group' => null,
            ],
            [
                'name' => 'Admin',
                'guard_name' => $defaultGuard,
                'sso_group' => null,
            ],
            [
                'name' => 'User',
                'guard_name' => $defaultGuard,
                'sso_group' => null,
            ],
            [
                'name' => 'Viewer',
                'guard_name' => $defaultGuard,
                'sso_group' => null,
            ],
        ];

        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::updateOrCreate([
                'name' => $role['name'],
            ], [
                'guard_name' => $role['guard_name'],
                'sso_group' => $role['sso_group'],
            ]);
        }
    }
}
