<?php

namespace App\Http\Requests\Favorite;

use App\Http\Requests\BaseApiRequest;

class ToggleFavoriteRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'حقل معرف الكتاب مطلوب',
            'book_id.exists' => 'الكتاب المحدد غير موجود',
        ];
    }
}
