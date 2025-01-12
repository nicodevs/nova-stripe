<?php

namespace Nicodevs\NovaStripe\Traits;

use Exception;
use Illuminate\Support\Arr;
use Stripe\Service\CustomerService;
use Stripe\Service\ProductService;
use Stripe\StripeClient;

trait SyncsWithStripe
{
    public function sync(): array
    {
        // TODO: Wrap in transaction
        $items = $this->getService()->all(['limit' => 100]);

        $records = [];
        foreach ($items->autoPagingIterator() as $item) {
            $records[] = Arr::only($item->toArray(), $this->getFillable());
        }

        $this->query()->delete();
        $this->insert($records);

        return $records;
    }

    private function getService(): CustomerService|ProductService
    {
        if (! property_exists($this, 'service') || empty($this->service)) {
            throw new Exception("The 'service' property must be defined in the class using the SyncsWithStripe trait.");
        }

        $stripe = new StripeClient(config('services.stripe.secret'));

        return $stripe->getService($this->service);
    }
}
