<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'sku' => [
                'required',
                'string',
                'max:100',
                'unique:products,sku',
                'regex:/^[A-Za-z0-9\-_]+$/'
            ],
            'product_name' => [
                'required',
                'string',
                'max:200'
            ],
            'description' => [
                'nullable',
                'string',
                'max:5000'
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99'
            ],
            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
                'max:999999'
            ],
            'category_id' => [
                'required',
                'exists:categories,category_id'
            ],
            'brand_id' => [
                'nullable',
                'exists:brands,brand_id'
            ],
            'photos' => [
                'nullable',
                'array',
                'max:10'
            ],
            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,gif',
                'max:2048'
            ],
            'status' => [
                'nullable',
                'in:active,inactive'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'กรุณาระบุรหัสสินค้า (SKU)',
            'sku.unique' => 'รหัสสินค้านี้ถูกใช้งานแล้ว',
            'sku.regex' => 'รหัสสินค้าต้องประกอบด้วยตัวอักษร ตัวเลข ขีดกลาง และขีดล่างเท่านั้น',
            'sku.max' => 'รหัสสินค้าต้องไม่เกิน 100 ตัวอักษร',
            'product_name.required' => 'กรุณาระบุชื่อสินค้า',
            'product_name.max' => 'ชื่อสินค้าต้องไม่เกิน 200 ตัวอักษร',
            'description.max' => 'คำอธิบายต้องไม่เกิน 5000 ตัวอักษร',
            'price.required' => 'กรุณาระบุราคา',
            'price.numeric' => 'ราคาต้องเป็นตัวเลข',
            'price.min' => 'ราคาต้องมากกว่าหรือเท่ากับ 0',
            'price.max' => 'ราคาต้องไม่เกิน 999,999,999.99',
            'stock_quantity.required' => 'กรุณาระบุจำนวนสินค้า',
            'stock_quantity.integer' => 'จำนวนสินค้าต้องเป็นจำนวนเต็ม',
            'stock_quantity.min' => 'จำนวนสินค้าต้องมากกว่าหรือเท่ากับ 0',
            'stock_quantity.max' => 'จำนวนสินค้าต้องไม่เกิน 999,999',
            'category_id.required' => 'กรุณาเลือกหมวดหมู่',
            'category_id.exists' => 'หมวดหมู่ไม่ถูกต้อง',
            'brand_id.exists' => 'แบรนด์ไม่ถูกต้อง',
            'photos.array' => 'รูปภาพต้องเป็นอาร์เรย์',
            'photos.max' => 'อัพโหลดได้สูงสุด 10 รูปภาพ',
            'photos.*.image' => 'ไฟล์ต้องเป็นรูปภาพ',
            'photos.*.mimes' => 'ไฟล์ต้องเป็น jpeg, png, jpg หรือ gif',
            'photos.*.max' => 'ขนาดไฟล์ต้องไม่เกิน 2MB',
            'status.in' => 'สถานะต้องเป็น active หรือ inactive',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge(['status' => 'active']);
        }
    }

    /**
     * Get validated data with defaults
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();

        // Ensure status has a default value
        $validated['status'] ??= 'active';

        return $validated;
    }
}