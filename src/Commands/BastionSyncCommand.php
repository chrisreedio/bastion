<?php

namespace ChrisReedIO\Bastion\Commands;

use ChrisReedIO\Bastion\Bastion;
use Illuminate\Console\Command;

class BastionSyncCommand extends Command
{
    public $signature = 'bastion:sync {panel? : Panel identifier to sync policies and permissions for}';

    public $description = 'Generates policies and permissions based on the current Filament resources';

    public function handle(): int
    {
        $panel = $this->argument('panel');

        Bastion::sync($panel);

        // All done
        $this->comment('All done');

        return self::SUCCESS;
    }
}
