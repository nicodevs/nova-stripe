<?php

namespace Nicodevs\NovaStripe\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Stripe\ApiResource;
use Stripe\Service\ChargeService;
use Stripe\Service\CustomerService;
use Stripe\Service\ProductService;
use Stripe\Service\SubscriptionService;
use Stripe\StripeClient;

abstract class BaseModel extends Model
{
    protected $guarded = [];

    public function sync(): array
    {
        $items = $this->getService()->all([
            'limit' => 100,
            'expand' => $this->expand ?? [],
        ]);

        $records = [];
        foreach ($items->autoPagingIterator() as $item) {
            $records[] = $this->prepareForInsert($item);
        }

        $this->query()->delete();
        $this->insert($records);

        return $records;
    }

    public function prepareForInsert(ApiResource $item): array
    {
        $result = [];

        foreach ($this->schema as $key => $value) {
            $fieldValue = $item->{$key} ?? null;

            if ($value === 'json') {
                if (is_object($fieldValue) && method_exists($fieldValue, 'toArray')) {
                    $result[$key] = json_encode($fieldValue->toArray());
                } elseif (is_array($fieldValue)) {
                    $result[$key] = json_encode($fieldValue);
                } else {
                    $result[$key] = $fieldValue;
                }
            } elseif ($value === 'datetime') {
                $result[$key] = Carbon::createFromTimestamp($fieldValue)->toDateTimeString();
            } else {
                $result[$key] = $key === 'customer_id' ? $item->customer : $fieldValue;
            }
        }

        return $result;
    }

    protected function stripeLink(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => 'https://dashboard.stripe.com/' . $this->service . '/' . $attributes['id'],
        );
    }

    private function getService(): ProductService|CustomerService|ChargeService|SubscriptionService
    {
        if (! property_exists($this, 'service') || empty($this->service)) {
            throw new Exception("The 'service' property must be defined in the class using the SyncsWithStripe trait.");
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        return $stripe->getService($this->service);
    }
}
