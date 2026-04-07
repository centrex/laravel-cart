<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        $table = config('laravel-cart.database.table', 'carts');

        Schema::create($table, function (Blueprint $table): void {
            $table->id();
            $table->string('instance', 64)->comment('Cart instance name, e.g. default or wishlist');
            $table->string('identifier')->comment('Session ID (guests) or user ID (auth)');
            $table->longText('content')->comment('Serialized array of CartItem objects');
            $table->timestamps();

            $table->unique(['instance', 'identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('laravel-cart.database.table', 'carts'));
    }
};
