<?php

use App\Exports\OrdersExport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

it('exports orders as Excel', function () {
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
                                                    'presentmentMoney' => ['amount' => '100.00']
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
                ]
            ]
        ], 200),
    ]);

    $this->actingAs(User::factory()->create());

    Excel::fake();

    $this->get('/export/orders')->assertOk();

    Excel::assertDownloaded('orders.xlsx', function (OrdersExport $export) {
        $collection = $export->collection();
        return $collection->first()['Name'] === '#123';
    });
});
