<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class OrdersExport implements FromCollection
{
    public function __construct(protected array $orders) {}

    public function collection()
    {
        return collect($this->orders)->map(function ($edge) {
            $node = $edge['node'];

            return [
                'ID' => $node['id'],
                'Name' => $node['name'],
                'Date' => $node['createdAt'],
                'Client' => trim(($node['customer']['firstName'] ?? '').' '.($node['customer']['lastName'] ?? '')),
                'Email' => $node['customer']['email'] ?? '',
                'Total' => $node['totalPrice'],
            ];
        });
    }
}
