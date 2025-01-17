<?php

namespace Nicodevs\NovaStripe\Resources;

use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Nicodevs\NovaStripe\Models\Product;

class Subscription extends BaseResource
{
    public static $model = \Nicodevs\NovaStripe\Models\Subscription::class;

    public static $title = 'description';

    public static $search = [
        'id',
    ];

    public static $with = ['customer'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->hideFromIndex(),

            BelongsTo::make('Customer')->sortable(),

            Badge::make('Status')->map([
                'active' => 'success',
                'incomplete' => 'warning',
                'incomplete_expired' => 'danger',
                'past_due' => 'warning',
                'canceled' => 'danger',
                'unpaid' => 'danger',
                'trialing' => 'info',
                'paused' => 'info',
            ])->sortable(),

            Text::make('Description')
                ->sortable(),

            ...collect([
                'Created',
                'Current Period Start',
                'Current Period End',
            ])->map(function ($key) {
                return Text::make($key)
                    ->displayUsing(fn ($value) => $this->formatDateTime($value));
            }),

            ...collect([
                'Trial Start',
                'Trial End',
                'Cancel At',
                'Canceled At',
                'Ended At',
            ])->map(function ($key) {
                return Text::make($key)
                    ->displayUsing(fn ($value) => $this->formatDateTime($value))
                    ->hideFromIndex();
            }),

            Text::make('Products', function () {
                return Product::whereIn('id', collect($this->items['data'])->pluck('price.product'))
                    ->get()
                    ->map(fn ($product) => '<a class="link-default" href="/nova/resources/products/' . $product->id . '">' . $product->name . '</a>')
                    ->join('<br>');
            })->asHtml()->hideFromIndex(),

            Text::make('Details', 'stripeLink')
                ->displayUsing(fn ($value) => '<a href="' . $value . '" target="_blank">Open in Stripe Dashboard</a>')
                ->asHtml()
                ->hideFromIndex(),

        ];
    }
}
