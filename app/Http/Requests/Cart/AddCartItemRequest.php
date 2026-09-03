<?php

namespace App\Http\Requests\Cart;

use App\Http\Requests\BaseApiRequest;

class AddCartItemRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'set_exact_quantity' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'حقل معرف الكتاب مطلوب',
            'book_id.exists' => 'الكتاب المحدد غير موجود',
            'quantity.integer' => 'الكمية يجب أن تكون رقماً صحيحاً',
            'quantity.min' => 'الكمية يجب أن تكون 1 على الأقل',
        ];
    }
}
