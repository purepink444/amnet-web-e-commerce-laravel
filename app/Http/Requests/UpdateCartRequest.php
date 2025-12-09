<?php

namespace App\Http\Requests;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
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
        return [
            'product_id' => [
                'required',
                'integer',
                'exists:products,product_id',
                function ($attribute, $value, $fail) {
                    $cartItem = $this->getCartItem();
                    if (!$cartItem) {
                        $fail('Cart item not found.');
                        return;
                    }

                    if ($cartItem->member_id !== auth()->user()->member->member_id) {
                        $fail('You do not have permission to modify this cart item.');
                    }
                },
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:99',
                function ($attribute, $value, $fail) {
                    $cartItem = $this->getCartItem();
                    if ($cartItem && $cartItem->product->stock_quantity < $value) {
                        $fail('Insufficient stock. Only ' . $cartItem->product->stock_quantity . ' items available.');
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
            'product_id.required' => 'Product ID is required.',
            'product_id.integer' => 'Product ID must be a number.',
            'product_id.exists' => 'Product not found.',
            'quantity.required' => 'Quantity is required.',
            'quantity.integer' => 'Quantity must be a number.',
            'quantity.min' => 'Quantity must be at least :min.',
            'quantity.max' => 'Quantity cannot exceed :max.',
        ];
    }

    /**
     * Get the cart item for this request.
     */
    public function getCartItem(): ?CartItem
    {
        if (!auth()->check() || !auth()->user()->member) {
            return null;
        }

        return CartItem::with('product')
            ->where('member_id', auth()->user()->member->member_id)
            ->where('product_id', $this->input('product_id'))
            ->first();
    }
}