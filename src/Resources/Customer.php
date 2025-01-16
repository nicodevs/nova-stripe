<?php

namespace Nicodevs\NovaStripe\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Url;
use Laravel\Nova\Http\Requests\NovaRequest;

class Customer extends BaseResource
{
    public static $model = \Nicodevs\NovaStripe\Models\Customer::class;

    public static $title = 'name';

    public static $search = [
        'id',
        'name',
        'email',
    ];

    public static $with = ['charges', 'subscriptions'];

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()
                ->hideFromIndex(),

            Text::make('Name')
                ->sortable(),

            Email::make('Email')->sortable(),

            Text::make('Created')
                ->displayUsing(fn ($value) => $this->formatDateTime($value))
                ->sortable(),

            Text::make('Phone')
                ->hideFromIndex(),

            Text::make('Full Address')
                ->hideFromIndex(),

            Number::make('Balance')
                ->hideFromIndex(),

            Boolean::make('Livemode')
                ->hideFromIndex(),

            Boolean::make('Delinquent')
                ->hideFromIndex(),

            Url::make('Details', 'stripeLink')
                ->displayUsing(fn () => 'Open in Stripe Dashboard')
                ->hideFromIndex(),

            HasMany::make('Charges'),

            HasMany::make('Subscriptions'),
        ];
    }
}
