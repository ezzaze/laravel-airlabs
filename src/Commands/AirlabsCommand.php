<?php

namespace Ezzaze\Airlabs\Commands;

use Illuminate\Console\Command;

class AirlabsCommand extends Command
{
    public $signature = 'airlabs';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
