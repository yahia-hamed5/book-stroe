<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseApiRequest;

class ForgotPasswordRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
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
            'email.exists' => 'البريد الإلكتروني غير مسجل لدينا',
        ];
    }
}
