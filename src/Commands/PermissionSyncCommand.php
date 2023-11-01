<?php

namespace ChrisReedIO\Bastion\Commands;

use ChrisReedIO\PolicyGenerator\PolicyGenerator;
use Filament\Facades\Filament;
use Illuminate\Console\Command;

class PermissionSyncCommand extends Command
{
    public $signature = 'bastion:sync';

    public $description = 'Generates policies and permissions based on the current Filament resources';

    public function handle(): int
    {
        // Figure out what resources exist
        $resources = Filament::getResources();
        dump($resources);

        // For each resource, generate a policy
        foreach ($resources as $resource) {
            $this->info("Generating policy for {$resource}...");
            PolicyGenerator::generate($resource);

            // Scan this policy for permissions to generate / sync
            dump(PolicyGenerator::getPolicyName($resource));
        }

        // All done
        $this->comment('All done');

        return self::SUCCESS;
    }
}
