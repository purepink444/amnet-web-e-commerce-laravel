<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Product::with(['category', 'brand', 'orderItems' => function($q) {
            $q->selectRaw('product_id, SUM(quantity) as total_sold, SUM(price * quantity) as total_revenue')
              ->groupBy('product_id');
        }]);

        if (!empty($this->filters['search'])) {
            $query->where('product_name', 'ILIKE', "%{$this->filters['search']}%");
        }

        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        if (!empty($this->filters['brand_id'])) {
            $query->where('brand_id', $this->filters['brand_id']);
        }

        $products = $query->get();

        // Calculate sales data
        foreach ($products as $product) {
            $orderItem = $product->orderItems->first();
            $product->total_sold = $orderItem ? $orderItem->total_sold : 0;
            $product->total_revenue = $orderItem ? $orderItem->total_revenue : 0;
        }

        return $products;
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Category',
            'Brand',
            'Price',
            'Stock Quantity',
            'Total Sold',
            'Total Revenue',
            'Status',
        ];
    }

    public function map($product): array
    {
        return [
            $product->product_id,
            $product->product_name,
            $product->category ? $product->category->category_name : 'N/A',
            $product->brand ? $product->brand->brand_name : 'N/A',
            $product->price,
            $product->stock_quantity,
            $product->total_sold ?? 0,
            $product->total_revenue ?? 0,
            $product->status,
        ];
    }
}