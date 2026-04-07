<?php

declare(strict_types = 1);

namespace Centrex\Cart\Commands;

use Illuminate\Console\Command;

class CartCommand extends Command
{
    public $signature = 'laravel-cart';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
