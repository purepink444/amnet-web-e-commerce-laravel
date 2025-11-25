<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CustomersReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = User::with(['role', 'orders' => function($q) {
            $q->selectRaw('user_id, COUNT(*) as total_orders, SUM(total_amount) as total_spent')
              ->groupBy('user_id');
        }])->whereHas('role', function($q) {
            $q->where('role_name', 'member');
        });

        if (!empty($this->filters['search'])) {
            $query->where(function($q) {
                $q->where('firstname', 'ILIKE', "%{$this->filters['search']}%")
                  ->orWhere('lastname', 'ILIKE', "%{$this->filters['search']}%")
                  ->orWhere('email', 'ILIKE', "%{$this->filters['search']}%");
            });
        }

        $customers = $query->get();

        // Calculate customer metrics
        foreach ($customers as $customer) {
            $orderData = $customer->orders->first();
            $customer->total_orders = $orderData ? $orderData->total_orders : 0;
            $customer->total_spent = $orderData ? $orderData->total_spent : 0;
            $customer->average_order_value = $customer->total_orders > 0
                ? $customer->total_spent / $customer->total_orders
                : 0;
        }

        return $customers;
    }

    public function headings(): array
    {
        return [
            'User ID',
            'Name',
            'Email',
            'Phone',
            'Total Orders',
            'Total Spent',
            'Average Order Value',
            'Registration Date',
        ];
    }

    public function map($customer): array
    {
        return [
            $customer->user_id,
            $customer->firstname . ' ' . $customer->lastname,
            $customer->email,
            $customer->phone ?? 'N/A',
            $customer->total_orders ?? 0,
            $customer->total_spent ?? 0,
            $customer->average_order_value ?? 0,
            $customer->created_at->format('Y-m-d H:i:s'),
        ];
    }
}