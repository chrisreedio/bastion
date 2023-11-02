<?php

namespace ChrisReedIO\Bastion\Commands;

use ChrisReedIO\Bastion\Bastion;
use Illuminate\Console\Command;

class BastionSyncCommand extends Command
{
    public $signature = 'bastion:sync';

    public $description = 'Generates policies and permissions based on the current Filament resources';

    public function handle(): int
    {
        Bastion::sync();

        // All done
        $this->comment('All done');

        return self::SUCCESS;
    }

}
