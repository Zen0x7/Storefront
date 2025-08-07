<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class OrderController extends Controller
{
    public function index(): Response
    {
        $shopify = new ShopifyService(
            config('services.shopify.url'),
            config('services.shopify.token')
        );

        try {
            $orders = $shopify->getAllOrders();
        } catch (ConnectionException $e) {
            Log::error('Shopify ConnectionException: '.$e->getMessage(), [
                'url' => config('services.shopify.url'),
                'token' => config('services.shopify.token'),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Orders/Index', [
                'orders' => [],
                'error' => 'No se pudo conectar a Shopify. Revisa la URL o el token.',
            ]);
        } catch (Throwable $e) {
            Log::error('Error inesperado en OrderController@index: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('Orders/Index', [
                'orders' => [],
                'error' => 'Ocurrió un error inesperado.',
            ]);
        }

        $data = collect($orders)->map(function ($edge) {
            $node = $edge['node'];

            return [
                'id' => $node['id'],
                'name' => $node['name'],
                'createdAt' => $node['createdAt'],
                'totalPrice' => $node['totalPrice'],
                'customer' => [
                    'name' => trim(($node['customer']['firstName'] ?? '').' '.($node['customer']['lastName'] ?? '')),
                    'email' => $node['customer']['email'] ?? '',
                ],
                'lineItems' => collect($node['lineItems']['edges'] ?? [])->map(function ($item) {
                    return [
                        'title' => $item['node']['title'],
                        'quantity' => $item['node']['quantity'],
                        'unitPrice' => data_get($item, 'node.originalUnitPriceSet.presentmentMoney.amount'),
                    ];
                }),
            ];
        });

        return Inertia::render('Orders/Index', [
            'orders' => $data->toArray(),
        ]);
    }
}
