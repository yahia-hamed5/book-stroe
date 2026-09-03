<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\General\SendContactMessageRequest;
use App\Http\Requests\General\SubscribeNewsletterRequest;
use App\Models\Branch;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all active store branches
     */
    public function branches(): JsonResponse
    {
        $branches = Branch::where('is_active', true)->get();

        return $this->successResponse($branches, 'تم جلب قائمة الفروع بنجاح');
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribeNewsletter(SubscribeNewsletterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        NewsletterSubscriber::create([
            'email' => $validated['email'],
        ]);

        return $this->successResponse(null, 'تم الاشتراك في النشرة البريدية بنجاح', 201);
    }

    /**
     * Submit contact us message
     */
    public function sendContactMessage(SendContactMessageRequest $request): JsonResponse
    {
        $validated = $request->validated();

        ContactMessage::create($validated);

        return $this->successResponse(null, 'تم استلام رسالتك بنجاح، وسنتواصل معك قريباً', 201);
    }


    /**
     * Get site settings / Features banner info
     */
    public function features(): JsonResponse
    {
        return $this->successResponse([
            'phone' => '01063888667',
            'email' => 'coding.arabic@gmail.com',
            'free_shipping_threshold' => 699,
            'features' => [
                [
                    'title' => 'شحن سريع',
                    'description' => 'سعر شحن موحد لجميع المحافظات ويصلك في أقل من 72 ساعة',
                    'icon' => 'feature-1.png',
                ],
                [
                    'title' => 'ضمان الجودة',
                    'description' => 'خامات عالية الجودة ومرونة في طلبات الاستبدال والاسترجاع',
                    'icon' => 'feature-2.png',
                ],
                [
                    'title' => 'دعم فني',
                    'description' => 'دعم فني على مدار اليوم للإجابة على أي استفسار لديك',
                    'icon' => 'feature-3.png',
                ],
                [
                    'title' => 'استبدال سهل',
                    'description' => 'يمكنك استبدال واسترجاع المنتج في حالة عدم مطابقة المواصفات',
                    'icon' => 'feature-4.png',
                ],
            ],
        ], 'تم جلب معلومات المتجر والمميزات');
    }
}
