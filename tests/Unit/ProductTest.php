<?php

use Nicodevs\NovaStripe\Models\Product;
use Nicodevs\NovaStripe\Services\StripeClientService;
use Stripe\Service\ProductService;

beforeEach(function (): void {
    $this->mockStripeClientService = Mockery::mock(StripeClientService::class);
    $this->mockProductService = Mockery::mock(ProductService::class);

    $mockResponse = Mockery::mock();
    $mockResponse->shouldReceive('autoPagingIterator')->andReturn(collect([
        (object) [
            'id' => 'prod_1',
            'object' => 'product',
            'created' => now()->timestamp,
            'active' => true,
            'name' => 'Foo',
            'default_price' => mockDefaultPrice([
                'id' => 'price_123',
                'object' => 'price',
                'currency' => 'usd',
                'unit_amount' => 100,
                'type' => 'recurring',
            ]),
        ],
        (object) [
            'id' => 'prod_2',
            'object' => 'product',
            'created' => now()->timestamp,
            'active' => true,
            'name' => 'Bar',
            'default_price' => mockDefaultPrice([
                'id' => 'price_456',
                'object' => 'price',
                'currency' => 'usd',
                'unit_amount' => 40,
                'type' => 'one_time',
            ]),
        ],
    ]));

    $this->mockProductService->shouldReceive('all')
        ->once()
        ->with(['limit' => 100, 'expand' => ['data.default_price']])
        ->andReturn($mockResponse);

    $this->mockStripeClientService->shouldReceive('getService')
        ->once()
        ->with('products')
        ->andReturn($this->mockProductService);

    app()->instance(StripeClientService::class, $this->mockStripeClientService);

    $this->model = new Product;
});

it('performs sync operation', function (): void {
    $result = $this->model->sync();

    expect($result)->toHaveCount(2);

    expect($result[0]['id'])->toBe('prod_1');
    expect($result[0]['name'])->toBe('Foo');
    expect($result[0]['default_price'])->toBe(json_encode([
        'id' => 'price_123',
        'object' => 'price',
        'currency' => 'usd',
        'unit_amount' => 100,
        'type' => 'recurring',
    ]));

    expect($result[1]['id'])->toBe('prod_2');
    expect($result[1]['name'])->toBe('Bar');
    expect($result[1]['default_price'])->toBe(json_encode([
        'id' => 'price_456',
        'object' => 'price',
        'currency' => 'usd',
        'unit_amount' => 40,
        'type' => 'one_time',
    ]));
});

it('queries correctly after sync operation', function (): void {
    $this->model->sync();

    $items = $this->model->all();
    expect($items)->toHaveCount(2);

    $item = $this->model->where('default_price->unit_amount', 40)->first();
    expect($item->name)->toBe('Bar');
    expect($item->id)->toBe('prod_2');
    expect($item->default_price['id'])->toBe('price_456');
});

it('builds correct stripe link attribute', function (): void {
    $this->model->sync();

    $item = $this->model->find('prod_1');
    expect($item->stripe_link)->toBe('https://dashboard.stripe.com/products/prod_1');
});

function mockDefaultPrice($fields): object
{
    return new class($fields)
    {
        public function __construct(private $fields) {}

        public function toArray()
        {
            return $this->fields;
        }
    };
}
