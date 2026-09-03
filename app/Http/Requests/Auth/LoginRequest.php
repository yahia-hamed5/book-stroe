<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseApiRequest;

class LoginRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'email.required' => 'حقل البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'password.required' => 'حقل كلمة المرور مطلوب',
        ];
    }
}
