<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;

it('renders the orders page with orders data', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'orders' => [
                    'edges' => [
                        [
                            'node' => [
                                'id' => 'gid://shopify/Order/123',
                                'name' => '#123',
                                'createdAt' => now()->toIso8601String(),
                                'totalPrice' => '100.00',
                                'customer' => [
                                    'firstName' => 'Ian',
                                    'lastName' => 'Torres',
                                    'email' => 'ian@example.com',
                                ],
                                'lineItems' => [
                                    'edges' => [
                                        [
                                            'node' => [
                                                'title' => 'Test Product',
                                                'quantity' => 1,
                                                'originalUnitPriceSet' => [
                                                    'presentmentMoney' => ['amount' => '100.00'],
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
                ],
            ],
        ], 200),
    ]);

    $this->actingAs(User::factory()->create());

    $this->get('/orders')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Orders/Index')
            ->has('orders', 1)
            ->where('orders.0.name', '#123')
            ->where('orders.0.customer.name', 'Ian Torres')
        );
});
