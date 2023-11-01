<?php

namespace ChrisReedIO\Bastion\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;

class BastionGenerate extends Command
{
    public $signature = 'bastion:generate';

    public $description = 'Generates policies based on the current Filament resources';

    public function handle(): int
    {
        // Figure out what resources exist
        $resources = Filament::getResources();
        dump($resources);

        // For each resource, generate a policy
        foreach ($resources as $resource) {
            $resourceName = $resource::uriKey();
            $this->info("Generating policy for {$resourceName}...");
            // Generate policy code here
        }

        // All done
        $this->comment('All done');

        return self::SUCCESS;
    }
}
