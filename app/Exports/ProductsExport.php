<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class ProductsExport implements FromCollection
{
    public function __construct(protected array $products) {}

    public function collection()
    {
        return collect($this->products)->map(function ($edge) {
            $node = $edge['node'];
            return [
                'ID' => $node['id'],
                'Title' => $node['title'],
                'SKU' => $node['variants']['edges'][0]['node']['sku'] ?? '',
                'Price' => $node['variants']['edges'][0]['node']['price'] ?? '',
                'Picture' => $node['images']['edges'][0]['node']['src'] ?? '',
            ];
        });
    }
}
