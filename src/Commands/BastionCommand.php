<?php

namespace ChrisReedIO\Bastion\Commands;

use Illuminate\Console\Command;

class BastionCommand extends Command
{
    public $signature = 'bastion';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
