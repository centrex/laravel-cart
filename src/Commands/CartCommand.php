<?php

declare(strict_types = 1);

namespace Centrex\Cart\Commands;

use Centrex\Cart\Facades\Cart;
use Illuminate\Console\Command;

class CartCommand extends Command
{
    public $signature = 'cart:clear
                        {--instance=default : Cart instance to clear}';

    public $description = 'Clear all items from a cart instance';

    public function handle(): int
    {
        $instance = (string) $this->option('instance');

        $cart = Cart::instance($instance);
        $count = $cart->count();

        if ($count === 0) {
            $this->info("Cart instance [{$instance}] is already empty.");

            return self::SUCCESS;
        }

        $cart->clear();

        $this->info("Cleared {$count} item(s) from cart instance [{$instance}].");

        return self::SUCCESS;
    }
}
