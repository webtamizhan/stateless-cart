<?php

namespace Webtamizhan\StatelessCart\Commands;

use Illuminate\Console\Command;

class StatelessCartCommand extends Command
{
    public $signature = 'stateless-cart';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
