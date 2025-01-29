<?php

use Nicodevs\NovaStripe\Models\Charge;
use Nicodevs\NovaStripe\Services\StripeClientService;
use Stripe\Service\ChargeService;

beforeEach(function () {
    $this->mockStripeClientService = Mockery::mock(StripeClientService::class);
    $this->mockChargeService = Mockery::mock(ChargeService::class);

    $mockResponse = Mockery::mock();
    $mockResponse->shouldReceive('autoPagingIterator')->andReturn(collect([
        (object) [
            'id' => 'ch_1',
            'object' => 'charge',
            'amount' => 1000,
            'currency' => 'usd',
            'paid' => true,
            'status' => 'succeeded',
            'created' => now()->timestamp,
            'customer' => 'cus_123',
            'payment_intent' => 'pi_123',
            'transfer_data' => ['foo' => 'bar'],
        ],
        (object) [
            'id' => 'ch_2',
            'object' => 'charge',
            'amount' => 500,
            'currency' => 'usd',
            'paid' => true,
            'status' => 'succeeded',
            'created' => now()->timestamp,
            'customer' => 'cus_456',
            'payment_intent' => 'pi_456',
            'transfer_data' => ['foo' => 'bar'],
        ],
    ]));

    $this->mockChargeService->shouldReceive('all')
        ->once()
        ->with(['limit' => 100, 'expand' => []])
        ->andReturn($mockResponse);

    $this->mockStripeClientService->shouldReceive('getService')
        ->once()
        ->with('charges')
        ->andReturn($this->mockChargeService);

    app()->instance(StripeClientService::class, $this->mockStripeClientService);

    $this->chargeModel = new Charge([], $this->mockStripeClientService);
});

it('performs sync operation', function () {
    $result = $this->chargeModel->sync();

    expect($result)->toHaveCount(2);

    expect($result[0]['id'])->toBe('ch_1');
    expect($result[0]['amount'])->toBe(1000);
    expect($result[0]['customer_id'])->toBe('cus_123');
    expect($result[0]['transfer_data'])->toBe(json_encode(['foo' => 'bar']));

    expect($result[1]['id'])->toBe('ch_2');
    expect($result[1]['amount'])->toBe(500);
    expect($result[1]['customer_id'])->toBe('cus_456');
    expect($result[1]['transfer_data'])->toBe(json_encode(['foo' => 'bar']));
});

it('model queries correctly after sync operation', function () {
    $this->chargeModel->sync();

    $charges = $this->chargeModel->all();
    expect($charges)->toHaveCount(2);

    $charge = $this->chargeModel->where('amount', 500)->first();
    expect($charge->id)->toBe('ch_2');
    expect($charge->transfer_data)->toBe(['foo' => 'bar']);
});

it('builds correct stripe link attribute', function () {
    $this->chargeModel->sync();

    $charge = $this->chargeModel->where('amount', 500)->first();
    expect($charge->stripe_link)->toBe('https://dashboard.stripe.com/payments/pi_456');
});
