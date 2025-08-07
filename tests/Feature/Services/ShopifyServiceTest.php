<?php

use App\Services\ShopifyService;
use Illuminate\Support\Facades\Http;

it('retrieves products from Shopify', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'products' => [
                    'edges' => [
                        ['node' => ['id' => '1', 'title' => 'Product 1']],
                    ],
                    'pageInfo' => ['hasNextPage' => false, 'endCursor' => null],
                ],
            ],
        ], 200),
    ]);

    $service = new ShopifyService('https://fake.myshopify.com', 'token');
    $result = $service->getAllProducts();

    expect($result)->toBeArray()
        ->and($result)->toHaveCount(1)
        ->and($result[0]['node']['title'])->toBe('Product 1');
});
