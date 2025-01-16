<?php

namespace Nicodevs\NovaStripe;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Nicodevs\NovaStripe\Resources\Charge;
use Nicodevs\NovaStripe\Resources\Customer;
use Nicodevs\NovaStripe\Resources\Product;
use Nicodevs\NovaStripe\Resources\Subscription;

class NovaStripe extends Tool
{
    public function boot(): void
    {
        Nova::script('nova-stripe-test', __DIR__ . '/../resources/js/index.js');

        Nova::resources([
            Customer::class,
            Product::class,
            Charge::class,
            Subscription::class,
        ]);
    }

    public function menu(Request $request): MenuSection
    {
        return MenuSection::make('Stripe', [
            MenuItem::make('Products', '/resources/products'),
            MenuItem::make('Customers', '/resources/customers'),
            MenuItem::make('Charges', '/resources/charges'),
            MenuItem::make('Subscriptions', '/resources/subscriptions'),
        ], 'credit-card');
    }
}
