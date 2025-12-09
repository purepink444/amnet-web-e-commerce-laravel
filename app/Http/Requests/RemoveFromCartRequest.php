<?php

namespace App\Http\Requests;

use App\Models\CartItem;
use Illuminate\Foundation\Http\FormRequest;

class RemoveFromCartRequest extends FormRequest
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

        return CartItem::where('member_id', auth()->user()->member->member_id)
            ->where('product_id', $this->input('product_id'))
            ->first();
    }
}