<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Exports\ProductsExport;
use App\Services\ShopifyService;
use Illuminate\Http\Client\ConnectionException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function exportProducts(): BinaryFileResponse
    {
        $shopify = new ShopifyService(config('services.shopify.url'), config('services.shopify.token'));
        $products = $shopify->getAllProducts();

        return Excel::download(new ProductsExport($products), 'products.xlsx');
    }

    /**
     * @throws ConnectionException
     */
    public function exportOrders(): BinaryFileResponse
    {
        $shopify = new ShopifyService(config('services.shopify.url'), config('services.shopify.token'));
        $orders = $shopify->getAllOrders();

        return Excel::download(new OrdersExport($orders), 'orders.xlsx');
    }
}
