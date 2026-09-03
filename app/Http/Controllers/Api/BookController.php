<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get books list with search, filter, and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Book::with(['category', 'author'])
            ->where('is_active', true);

        // Search by title, author_name, description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        } elseif ($request->filled('category_slug')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }

        // Filter by Author
        if ($request->filled('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Filter by Language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // Filter by Featured
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'latest');
        match ($sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'title_asc' => $query->orderBy('title', 'asc'),
            default => $query->latest(),
        };

        $perPage = $request->get('per_page', 12);
        $books = $query->paginate($perPage);

        return $this->successResponse($books, 'تم جلب قائمة الكتب بنجاح');
    }

    /**
     * Get single book details
     */
    public function show($id): JsonResponse
    {
        $book = Book::with(['category', 'author', 'reviews.user'])
            ->where('is_active', true)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('slug', $id);
            })
            ->first();

        if (!$book) {
            return $this->errorResponse('الكتاب غير موجود', 404);
        }

        // Related books from same category
        $relatedBooks = Book::where('is_active', true)
            ->where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->limit(4)
            ->get();

        return $this->successResponse([
            'book' => $book,
            'related_books' => $relatedBooks,
        ], 'تم جلب تفاصيل الكتاب بنجاح');
    }

    /**
     * Get featured books (for home page)
     */
    public function featured(): JsonResponse
    {
        $books = Book::with(['category', 'author'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->limit(8)
            ->get();

        return $this->successResponse($books, 'تم جلب الكتب المميزة بنجاح');
    }
}
