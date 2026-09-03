<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all active categories
     */
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->withCount(['books' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get();

        return $this->successResponse($categories, 'تم جلب التصنيفات بنجاح');
    }

    /**
     * Get single category with its books
     */
    public function show($id): JsonResponse
    {
        $category = Category::where('is_active', true)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('slug', $id);
            })
            ->with(['books' => function ($query) {
                $query->where('is_active', true);
            }])
            ->first();

        if (!$category) {
            return $this->errorResponse('التصنيف غير موجود', 404);
        }

        return $this->successResponse($category, 'تم جلب بيانات التصنيف بنجاح');
    }
}
