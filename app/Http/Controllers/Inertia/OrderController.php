<?php

namespace App\Http\Controllers\Inertia;

use App\Http\Controllers\Controller;
use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function index(): Response
    {
        $shopify = new ShopifyService(
            config('services.shopify.url'),
            config('services.shopify.token')
        );

        $orders = $shopify->getAllOrders();

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
