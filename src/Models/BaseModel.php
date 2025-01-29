<?php

namespace Nicodevs\NovaStripe\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Nicodevs\NovaStripe\Services\StripeClientService;

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

    public function prepareForInsert(object $item): array
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
            get: fn ($value, array $attributes): string => 'https://dashboard.stripe.com/' . $this->service . '/' . $attributes['id'],
        );
    }

    private function getService()
    {
        if (! property_exists($this, 'service') || empty($this->service)) {
            throw new Exception("The 'service' property must be defined and valid.");
        }

        $client = app(StripeClientService::class);

        return $client->getService($this->service);
    }
}
