<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ShopifyService
{
    public function __construct(protected string $shop, protected string $token) {}

    /**
     * @throws ConnectionException
     */
    public function query(string $query): array
    {
        $url = "{$this->shop}/admin/api/2025-07/graphql.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->token,
            'Content-Type' => 'application/json',
        ])->post($url, [
            'query' => $query,
        ]);

        return $response->json();
    }

    /**
     * @throws ConnectionException
     */
    public function getAllProducts(): array
    {
        $products = [];
        $cursor = null;

        if (Cache::has('products')) {
            return Cache::get('products');
        }

        do {
            $after = $cursor ? ", after: \"{$cursor}\"" : '';
            $query = <<<GQL
{
  products(first: 50{$after}) {
    pageInfo {
      hasNextPage
      endCursor
    }
    edges {
      node {
        id
        title
        variants(first: 1) {
          edges {
            node {
              sku
              price
            }
          }
        }
        images(first: 1) {
          edges {
            node {
              src
            }
          }
        }
      }
    }
  }
}
GQL;
            $result = $this->query($query);
            $edges = data_get($result, 'data.products.edges', []);
            $products = array_merge($products, $edges);
            $cursor = data_get($result, 'data.products.pageInfo.endCursor');
            $hasNextPage = data_get($result, 'data.products.pageInfo.hasNextPage');
        } while ($hasNextPage);

        Cache::put('products', $products, now()->addMinute());

        return $products;
    }

    /**
     * @throws ConnectionException
     */
    public function getAllOrders(): array
    {
        $orders = [];
        $cursor = null;

        if (Cache::has('orders')) {
            return Cache::get('orders');
        }

        $since = now()->subDays(30)->toDateString();

        do {
            $after = $cursor ? ", after: \"{$cursor}\"" : '';
            $query = <<<GQL
{
  orders(first: 50{$after}, query: "createdAt:>={$since}") {
    pageInfo {
      hasNextPage
      endCursor
    }
    edges {
      node {
        id
        name
        createdAt
        totalPrice
        customer {
          firstName
          lastName
          email
        }
        lineItems(first: 50) {
          edges {
            node {
              title
              quantity
              originalUnitPriceSet {
                presentmentMoney {
                  amount
                }
              }
            }
          }
        }
      }
    }
  }
}
GQL;

            $result = $this->query($query);
            $edges = data_get($result, 'data.orders.edges', []);
            $orders = array_merge($orders, $edges);
            $cursor = data_get($result, 'data.orders.pageInfo.endCursor');
            $hasNextPage = data_get($result, 'data.orders.pageInfo.hasNextPage');
        } while ($hasNextPage);

        Cache::put('orders', $orders, now()->addMinute());

        return $orders;
    }
}
