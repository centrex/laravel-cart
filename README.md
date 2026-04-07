# Laravel Cart

[![Latest Version on Packagist](https://img.shields.io/packagist/v/centrex/laravel-cart.svg?style=flat-square)](https://packagist.org/packages/centrex/laravel-cart)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-cart/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/centrex/laravel-cart/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/centrex/laravel-cart/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/centrex/laravel-cart/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/centrex/laravel-cart?style=flat-square)](https://packagist.org/packages/centrex/laravel-cart)

A full-featured shopping cart for Laravel. Supports **session**, **cookie**, and **database** storage drivers, multiple named cart instances (e.g. wishlist), Livewire components (`CartIcon`, `CartDrawer`), and a REST API — all swappable via a single config key.

## Installation

```bash
composer require centrex/laravel-cart
php artisan vendor:publish --tag="laravel-cart-config"
```

For the database driver, run:

```bash
php artisan vendor:publish --tag="laravel-cart-migrations"
php artisan migrate
```

## Configuration

```bash
php artisan vendor:publish --tag="laravel-cart-config"
```

```php
// config/laravel-cart.php
'driver'          => env('CART_DRIVER', 'session'),   // session | cookie | database
'default_instance'=> 'default',
'tax'             => env('CART_TAX', 0),              // integer percent, e.g. 15
'cookie_lifetime' => env('CART_COOKIE_LIFETIME', 43200),  // minutes (default 30 days)
'database' => [
    'connection' => env('CART_DB_CONNECTION'),
    'table'      => 'carts',
],
```

### Drivers

| Driver | Persists across requests | Auth-aware | Best for |
| --- | --- | --- | --- |
| `session` | Until session expires | No | Default, server-rendered apps |
| `cookie` | 30 days (configurable) | No | Guest carts, headless/SPA |
| `database` | Permanently (until cleared) | Yes — uses user ID for auth, session ID for guests | Authenticated users, cart recovery |

## Usage

### Facade

```php
use Centrex\Cart\Facades\Cart;

// Add item (merges qty if same rowId already exists)
$item = Cart::add(
    id:      $product->id,
    name:    $product->name,
    qty:     2,
    price:   $product->price,
    options: ['color' => 'red', 'size' => 'M'],
);

echo $item->rowId;    // md5 hash of id + sorted options
echo $item->subtotal; // qty × price

// Update quantity
Cart::update($item->rowId, 5);

// Remove item
Cart::remove($item->rowId);

// Clear everything
Cart::clear();
```

### Reading the cart

```php
// All items — Collection<string, CartItem>
Cart::content();

// Single item (throws CartItemNotFoundException if missing)
Cart::get($rowId);

// Aggregates
Cart::count();      // total units (sum of all qty)
Cart::lines();      // number of distinct line items
Cart::subtotal();   // sum of all item subtotals
Cart::tax();        // subtotal × (tax% / 100)
Cart::total();      // subtotal + tax
Cart::isEmpty();
```

### CartItem properties

```php
$item->rowId;    // string — md5(id + sorted options)
$item->id;       // string|int — your product ID
$item->name;     // string
$item->qty;      // int
$item->price;    // float — unit price
$item->options;  // array — ['color' => 'red', 'size' => 'M']
$item->subtotal; // float — qty × price
$item->toArray();
```

### Multiple cart instances

Use `instance()` to work with independent named carts. Returns a new `Cart` object — does not mutate the original singleton.

```php
// Default cart
Cart::add(1, 'Widget', 1, 29.99);

// Wishlist cart
$wishlist = Cart::instance('wishlist');
$wishlist->add(2, 'Gadget', 1, 99.00);

Cart::count();              // 1 (default)
$wishlist->count();         // 1 (wishlist)
```

### Events

Listen to cart events in your application's `EventServiceProvider`:

```php
use Centrex\Cart\Events\CartItemAdded;
use Centrex\Cart\Events\CartItemUpdated;
use Centrex\Cart\Events\CartItemRemoved;
use Centrex\Cart\Events\CartCleared;

Event::listen(CartItemAdded::class, function (CartItemAdded $event) {
    // $event->item     — CartItem
    // $event->instance — 'default'
});
```

### Cookie storage — guest carts

```env
CART_DRIVER=cookie
CART_COOKIE_LIFETIME=43200  # 30 days in minutes
```

> **Note:** Cookie writes are queued for the HTTP response. A `get()` call in the same request reads what the browser already sent, not what was queued. This is standard browser behaviour.

### Database storage — persistent & auth-aware

```env
CART_DRIVER=database
```

- Guests are identified by `session()->getId()`.
- Authenticated users are identified by `auth()->id()`.
- Cart rows are keyed on `(instance, identifier)` — unique index.

To migrate a guest cart to a user after login:

```php
// In your LoginController / AuthenticatedSessionController
use Centrex\Cart\Models\StoredCart;

StoredCart::where('identifier', session()->getId())
    ->update(['identifier' => (string) auth()->id()]);
```

---

## Livewire Components

Requires `livewire/livewire ^3`.

### CartIcon

Displays a shopping bag icon with an item count badge. Updates automatically when the cart changes.

```blade
{{-- In your navbar / header --}}
<livewire:cart-icon />
```

Clicking the icon dispatches the `open-cart` browser event, which opens `CartDrawer`.

### CartDrawer

A slide-out panel showing the full cart with quantity controls, item removal, and totals.

```blade
{{-- Once, anywhere in your layout (e.g. before </body>) --}}
<livewire:cart-drawer />
```

Open the drawer from any element:

```blade
{{-- Alpine.js dispatch --}}
<button @click="$dispatch('open-cart')">View Cart</button>

{{-- Or from a Livewire component --}}
$this->dispatch('open-cart');
```

### Triggering a cart update from your own Livewire components

After adding an item to the cart in a Livewire component, dispatch `cart-updated` so `CartIcon` and `CartDrawer` refresh automatically:

```php
// In your ProductCard or similar Livewire component
public function addToCart(int $productId): void
{
    $product = Product::findOrFail($productId);

    Cart::add($product->id, $product->name, 1, $product->price);

    $this->dispatch('cart-updated');   // refreshes CartIcon badge + CartDrawer
    $this->dispatch('open-cart');      // optionally open the drawer
}
```

### Publish views for customisation

```bash
php artisan vendor:publish --tag="laravel-cart-views"
# → resources/views/vendor/laravel-cart/livewire/cart-icon.blade.php
# → resources/views/vendor/laravel-cart/livewire/cart-drawer.blade.php
```

---

## REST API

All endpoints are prefixed with `api` (configurable via `api_prefix`) and use the `api` middleware (configurable via `api_middleware`).

| Method | Endpoint | Description |
| --- | --- | --- |
| `GET` | `/api/cart` | Get full cart (items, count, subtotal, tax, total) |
| `POST` | `/api/cart/items` | Add item to cart |
| `PUT` | `/api/cart/items/{rowId}` | Update item quantity |
| `DELETE` | `/api/cart/items/{rowId}` | Remove item |
| `DELETE` | `/api/cart` | Clear cart |

### GET /api/cart

```json
{
  "data": {
    "instance": "default",
    "items": [
      {
        "row_id": "b8b0b1b2...",
        "id": 1,
        "name": "Widget",
        "qty": 2,
        "price": 29.99,
        "subtotal": 59.98,
        "options": { "color": "red" }
      }
    ],
    "count": 2,
    "lines": 1,
    "subtotal": 59.98,
    "tax": 0.0,
    "total": 59.98
  }
}
```

### POST /api/cart/items

```json
{
  "id": 1,
  "name": "Widget",
  "qty": 2,
  "price": 29.99,
  "options": { "color": "red", "size": "M" }
}
```

### PUT /api/cart/items/{rowId}

```json
{ "qty": 5 }
```

### DELETE /api/cart/items/{rowId}

Returns `204 No Content`.

### DELETE /api/cart

Returns `204 No Content`.

---

## Testing

```bash
composer test        # full suite
composer test:unit   # pest only
composer test:types  # phpstan
composer lint        # pint
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [rochi88](https://github.com/centrex)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
