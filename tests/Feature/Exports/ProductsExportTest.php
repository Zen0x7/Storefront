<?php

use App\Exports\ProductsExport;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;

it('exports products as Excel', function () {
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
                                        ['node' => ['sku' => 'Z123', 'price' => '49.99']]
                                    ]
                                ],
                                'images' => [
                                    'edges' => [
                                        ['node' => ['src' => 'https://example.com/image.jpg']]
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

    $this->get('/export/products')->assertOk();

    Excel::assertDownloaded('products.xlsx', function (ProductsExport $export) {
        $collection = $export->collection();
        return $collection->first()['Title'] === 'Zapato de prueba';
    });
});
