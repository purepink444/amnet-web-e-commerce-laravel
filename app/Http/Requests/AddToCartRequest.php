<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddToCartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $productId = $this->route('productId');

        return [
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99',
                function ($attribute, $value, $fail) use ($productId) {
                    $product = Product::find($productId);
                    if (!$product) {
                        $fail('Product not found.');
                        return;
                    }

                    if ($product->status !== 'active') {
                        $fail('Product is not available.');
                        return;
                    }

                    if ($product->stock_quantity < $value) {
                        $fail('Insufficient stock. Only ' . $product->stock_quantity . ' items available.');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least :min.',
            'quantity.max' => 'Quantity cannot exceed :max.',
        ];
    }

    /**
     * Get the validated product.
     */
    public function getProduct(): Product
    {
        return Product::findOrFail($this->route('productId'));
    }
}