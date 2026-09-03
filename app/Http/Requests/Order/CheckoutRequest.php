<?php

namespace App\Http\Requests\Order;

use App\Http\Requests\BaseApiRequest;

class CheckoutRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'full_address' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string', 'in:cash_on_delivery'],
            // Optional direct items if checking out without persistent user cart
            'items' => ['nullable', 'array'],
            'items.*.book_id' => ['required_with:items', 'exists:books,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب',
            'last_name.required' => 'الاسم الأخير مطلوب',
            'city.required' => 'المدينة / المحافظة مطلوبة',
            'full_address.required' => 'العنوان بالتفصيل مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'items.*.book_id.exists' => 'أحد الكتب المحددة غير موجود',
        ];
    }
}
