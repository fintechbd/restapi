<?php

namespace Fintech\RestApi\Commands;

use Illuminate\Console\Command;

class RestApiCommand extends Command
{
    public $signature = 'restapi';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
