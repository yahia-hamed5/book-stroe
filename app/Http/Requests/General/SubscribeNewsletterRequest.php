<?php

namespace App\Http\Requests\General;

use App\Http\Requests\BaseApiRequest;

class SubscribeNewsletterRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:newsletter_subscribers,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'حقل البريد الإلكتروني مطلوب',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
            'email.unique' => 'تم الاشتراك بهذا البريد الإلكتروني مسبقاً',
        ];
    }
}
