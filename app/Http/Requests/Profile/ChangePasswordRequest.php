<?php

namespace App\Http\Requests\Profile;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية',
            'new_password.required' => 'يرجى إدخال كلمة المرور الجديدة',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ];
    }
}
