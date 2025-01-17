
## Installation

After installing Nova, add this to your `composer.json`:

```diff
"repositories": {
    "nova": {
        "type": "composer",
        "url": "https://nova.laravel.com"
-    }
+    },
+    "0": {
+        "type": "vcs",
+        "url": "git@github.com:nicodevs/nova-stripe.git"
+    }
}
```

Then install this package via [Composer](https://getcomposer.org/):

```bash
composer require nicodevs/nova-stripe:dev-main
```

## Usage

Add your [Stripe key and secret](https://stripe.com/docs/keys#obtain-api-keys) values to your `.env` file:

```
STRIPE_KEY=
STRIPE_SECRET=
```

Add a `stripe` element to your `config/services.php` configuration file:

```php
'stripe' => [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
],
```

Register the `NovaStripe` tool in `app/Providers/NovaServiceProvider`:

```php
public function tools()
{
    return [
        new \Nicodevs\NovaStripe\NovaStripe,
    ];
}
```
