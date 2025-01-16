<?php

namespace Nicodevs\NovaStripe\Actions;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\BooleanGroup;
// use Illuminate\Contracts\Queue\ShouldQueue;
use Laravel\Nova\Http\Requests\NovaRequest;
use Nicodevs\NovaStripe\Models\Charge;
use Nicodevs\NovaStripe\Models\Customer;
use Nicodevs\NovaStripe\Models\Product;
use Nicodevs\NovaStripe\Models\Subscription;

class Sync extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Sync With Stripe';

    public $confirmText = null;

    public $confirmButtonText = 'Sync With Stripe';

    public function handle(ActionFields $fields)
    {
        foreach (array_keys(array_filter($fields->resources)) as $resource) {
            $model = match ($resource) {
                'Products' => app(Product::class),
                'Customers' => app(Customer::class),
                'Charges' => app(Charge::class),
                'Subscriptions' => app(Subscription::class),
                default => throw new Exception("Model not found for resource: {$resource}"),
            };

            $model->sync();
        }

        return ActionResponse::message('Sync completed!');
    }

    public function fields(NovaRequest $request)
    {
        return [
            BooleanGroup::make('Select the resources you wish to sync:', 'resources')
                ->options([
                    'Products',
                    'Customers',
                    'Charges',
                    'Subscriptions',
                ])
                ->rules('required')
                ->stacked(),
        ];
    }
}
