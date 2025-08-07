<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia;

it('renders the products page with products data', function () {
    Http::fake([
        '*' => Http::response([
            'data' => [
                'products' => [
                    'edges' => [
                        [
                            'node' => [
                                'id' => 'gid://shopify/Product/111',
                                'title' => 'Zapato de prueba',
                                'variants' => [
                                    'edges' => [
                                        ['node' => ['sku' => 'Z123', 'price' => '49.99']],
                                    ],
                                ],
                                'images' => [
                                    'edges' => [
                                        ['node' => ['src' => 'https://example.com/image.jpg']],
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

    $this->get('/products')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Products/Index')
            ->has('products', 1)
            ->where('products.0.title', 'Zapato de prueba')
            ->where('products.0.sku', 'Z123')
        );
});
