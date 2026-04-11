<div x-data x-cloak>
    {{-- Backdrop --}}
    <div
        x-show="$wire.open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/40"
        wire:click="closeDrawer"
    ></div>

    {{-- Drawer panel --}}
    <div
        x-show="$wire.open"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col bg-base-100 shadow-2xl"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-base-200 px-6 py-4">
            <h2 class="text-lg font-semibold text-base-content">
                Shopping Cart
                @if($items->isNotEmpty())
                    <span class="ml-2 text-sm font-normal text-base-content/50">({{ $items->count() }} {{ Str::plural('item', $items->count()) }})</span>
                @endif
            </h2>
            <button wire:click="closeDrawer" class="btn btn-ghost btn-sm btn-circle" aria-label="Close cart">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- Flash message --}}
        @if($flash)
            <div class="mx-6 mt-3 alert alert-warning text-sm py-2">{{ $flash }}</div>
        @endif

        {{-- Items --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">
            @if($items->isEmpty())
                <div class="flex flex-col items-center justify-center h-full gap-4 text-center py-16">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 text-base-content/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>
                    <p class="font-semibold text-base-content/60">Your cart is empty</p>
                    <button wire:click="closeDrawer" class="btn btn-primary btn-sm">Continue Shopping</button>
                </div>
            @else
                <ul class="divide-y divide-base-200 space-y-0">
                    @foreach($items as $item)
                        <li class="flex gap-4 py-4">
                            {{-- Placeholder thumbnail --}}
                            <div class="h-16 w-16 flex-shrink-0 rounded-lg bg-base-200 flex items-center justify-center text-base-content/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
                                </svg>
                            </div>

                            {{-- Details --}}
                            <div class="flex flex-1 flex-col gap-1">
                                <div class="flex items-start justify-between">
                                    <p class="font-medium text-sm text-base-content">{{ $item->name }}</p>
                                    <button
                                        wire:click="removeItem('{{ $item->rowId }}')"
                                        wire:loading.attr="disabled"
                                        class="btn btn-ghost btn-xs btn-circle text-error ml-2 flex-shrink-0"
                                        aria-label="Remove {{ $item->name }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>

                                @if(!empty($item->options))
                                    <p class="text-xs text-base-content/50">
                                        {{ collect($item->options)->map(fn($v,$k) => ucfirst($k).': '.$v)->implode(', ') }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between mt-1">
                                    {{-- Qty stepper --}}
                                    <div class="flex items-center gap-1">
                                        <button
                                            wire:click="updateQty('{{ $item->rowId }}', {{ $item->qty - 1 }})"
                                            class="btn btn-ghost btn-xs btn-circle"
                                            @if($item->qty <= 1) disabled @endif
                                        >−</button>
                                        <span class="w-6 text-center text-sm font-medium">{{ $item->qty }}</span>
                                        <button
                                            wire:click="updateQty('{{ $item->rowId }}', {{ $item->qty + 1 }})"
                                            class="btn btn-ghost btn-xs btn-circle"
                                        >+</button>
                                    </div>

                                    <p class="text-sm font-semibold text-base-content">
                                        {{ number_format($item->subtotal, 2) }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Clear cart --}}
                <div class="mt-4 text-right">
                    <button
                        wire:click="clearCart"
                        wire:confirm="Clear all items from your cart?"
                        class="btn btn-ghost btn-xs text-error"
                    >
                        Clear cart
                    </button>
                </div>
            @endif
        </div>

        {{-- Footer totals + CTA --}}
        @if($items->isNotEmpty())
            <div class="border-t border-base-200 px-6 py-5 space-y-2">
                <div class="flex justify-between text-sm text-base-content/70">
                    <span>Subtotal</span>
                    <span>{{ number_format($subtotal, 2) }}</span>
                </div>
                @if($tax > 0)
                    <div class="flex justify-between text-sm text-base-content/70">
                        <span>Tax ({{ config('laravel-cart.tax') }}%)</span>
                        <span>{{ number_format($tax, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-base text-base-content pt-1 border-t border-base-200">
                    <span>Total</span>
                    <span>{{ number_format($total, 2) }}</span>
                </div>

                <a href="{{ url(config('laravel-cart.checkout_url', '/checkout')) }}" class="btn btn-primary w-full mt-3">
                    Proceed to Checkout
                </a>
            </div>
        @endif
    </div>
</div>
