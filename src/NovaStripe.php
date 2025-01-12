<?php

namespace Nicodevs\NovaStripe;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Nicodevs\NovaStripe\Resources\Customer;
use Nicodevs\NovaStripe\Resources\Product;
use Nicodevs\NovaStripe\Resources\Charge;
use Nicodevs\NovaStripe\Resources\Subscription;

class NovaStripe extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::resources([
            Customer::class,
            Product::class,
            Charge::class,
            Subscription::class,
        ]);
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @return mixed
     */
    public function menu(Request $request)
    {
        return MenuSection::make('Nova Stripe', [
            MenuItem::make('Products', '/resources/products'),
            MenuItem::make('Customers', '/resources/customers'),
            MenuItem::make('Charges', '/resources/charges'),
            MenuItem::make('Subscriptions', '/resources/subscriptions'),
        ], 'credit-card')->path('/nova-stripe');
    }
}
