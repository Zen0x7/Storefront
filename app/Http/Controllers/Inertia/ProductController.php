<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProductController extends Controller
{
    public function index(): Response
    {
        $shopify = new ShopifyService(config('services.shopify.url'), config('services.shopify.token'));

        try {
            $products = $shopify->getAllProducts();
        } catch (ConnectionException $e) {
            Log::error('Shopify ConnectionException en ProductController@index: '.$e->getMessage(), [
                'url' => config('services.shopify.url'),
                'token' => config('services.shopify.token'),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Products/Index', [
                'products' => [],
                'error' => 'No se pudo conectar a Shopify para cargar los productos.',
            ]);
        } catch (Throwable $e) {
            Log::error('Error inesperado en ProductController@index: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Products/Index', [
                'products' => [],
                'error' => 'Ocurrió un error inesperado al cargar los productos.',
            ]);
        }

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
