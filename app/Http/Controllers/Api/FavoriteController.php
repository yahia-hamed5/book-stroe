<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favorite\ToggleFavoriteRequest;
use App\Models\Book;
use App\Models\Favorite;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get user's favorites list
     */
    public function index(Request $request): JsonResponse
    {
        $favorites = Favorite::where('user_id', $request->user()->id)
            ->with(['book.category'])
            ->latest()
            ->get();

        return $this->successResponse($favorites, 'تم جلب قائمة المفضلة بنجاح');
    }

    /**
     * Toggle book in favorites (Add/Remove)
     */
    public function toggle(ToggleFavoriteRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $request->user()->id;
        $bookId = $validated['book_id'];

        $favorite = Favorite::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return $this->successResponse([
                'is_favorite' => false,
                'book_id' => $bookId,
            ], 'تم حذف الكتاب من المفضلة');
        }

        Favorite::create([
            'user_id' => $userId,
            'book_id' => $bookId,
        ]);

        return $this->successResponse([
            'is_favorite' => true,
            'book_id' => $bookId,
        ], 'تمت إضافة الكتاب إلى المفضلة');
    }
}

