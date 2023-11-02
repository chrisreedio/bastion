<?php

namespace ChrisReedIO\Bastion;

use ChrisReedIO\PolicyGenerator\PolicyGenerator;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

use function array_diff;
use function class_basename;
use function collect;
use function dump;
use function get_class_methods;

class Bastion
{
    public static function sync()
    {
        // Figure out what resources exist
        $resources = Filament::getResources();
        dump($resources);

        // TODO! Remove me!
        Permission::truncate();

        // For each resource, generate a policy
        foreach ($resources as $resource) {
            dump("Generating policy for {$resource}...");
            PolicyGenerator::generate($resource);

            self::syncResource($resource);
        }
    }

    protected static function syncResource(string $resource): bool
    {
        // Scan this policy for permissions to generate / sync
        $policyName = PolicyGenerator::getPolicyName($resource);
        $fullModelName = $resource::getModel();
        $modelName = Str::snake(class_basename($fullModelName));
        dump("Scanning {$policyName} [$resource => $fullModelName] for permissions...");

        $methods = array_diff(get_class_methods($policyName), get_class_methods(HandlesAuthorization::class));
        // $permissionNames = collect($methods)->map(fn($method) => Str::snake($method) . '::' . $modelName)->all();
        $permissionNames = collect($methods)->map(fn ($method) => Str::snake($method))->all();

        dump($permissionNames);

        foreach ($permissionNames as $permissionName) {
            Permission::create([
                'display_name' => $permissionName,
                'name' => $permissionName . '::' . $modelName,
                'resource' => $resource,
            ]);
        }

        dump(Permission::all()->toArray());
        // foreach ($permissionNames as $method) {
        //     dump($method . '::' . $modelName);
        // }

        // dump($methods);

        return true;
    }
}
