<?php


namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function index(): Response
    {
        $shopify = new ShopifyService(config('services.shopify.url'), config('services.shopify.token'));
        $products = $shopify->getAllProducts();

        $data = collect($products)->map(function ($edge) {
            $node = $edge['node'];
            return [
                'id' => $node['id'],
                'title' => $node['title'],
                'sku' => $node['variants']['edges'][0]['node']['sku'] ?? '',
                'price' => $node['variants']['edges'][0]['node']['price'] ?? '',
                'image' => $node['images']['edges'][0]['node']['src'] ?? '',
            ];
        });

        return Inertia::render('Products/Index', [
            'products' => $data,
        ]);
    }
}

