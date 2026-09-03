<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Models\Book;
use App\Models\CartItem;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get user cart items and total calculation
     */
    public function index(Request $request): JsonResponse
    {
        $cartItems = CartItem::where('user_id', $request->user()->id)
            ->with(['book.category'])
            ->get();

        $subtotal = 0;
        $totalItems = 0;

        foreach ($cartItems as $item) {
            $itemTotal = $item->quantity * ($item->book->price ?? 0);
            $item->item_total = $itemTotal;
            $subtotal += $itemTotal;
            $totalItems += $item->quantity;
        }

        // Free shipping if total >= 699 as shown in the UI header!
        $shippingFee = ($subtotal >= 699 || $subtotal == 0) ? 0 : 50.00;
        $total = $subtotal + $shippingFee;

        return $this->successResponse([
            'items' => $cartItems,
            'total_items_count' => $totalItems,
            'subtotal' => round($subtotal, 2),
            'shipping_fee' => round($shippingFee, 2),
            'free_shipping_threshold' => 699.00,
            'is_free_shipping' => $subtotal >= 699 && $subtotal > 0,
            'total' => round($total, 2),
        ], 'تم جلب محتويات سلة التسوق');
    }

    /**
     * Add book to cart or update quantity
     */
    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $userId = $request->user()->id;
        $bookId = $validated['book_id'];
        $quantity = $validated['quantity'] ?? 1;

        $book = Book::find($bookId);
        if (!$book || !$book->is_active) {
            return $this->errorResponse('هذا الكتاب غير متاح حالياً', 400);
        }

        if ($book->stock < $quantity) {
            return $this->errorResponse('الكمية المطلوبة غير متوفرة في المخزون (المتوفر: ' . $book->stock . ')', 400);
        }

        $cartItem = CartItem::where('user_id', $userId)
            ->where('book_id', $bookId)
            ->first();

        if ($cartItem) {
            $newQuantity = $request->has('set_exact_quantity') ? $quantity : ($cartItem->quantity + $quantity);
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'user_id' => $userId,
                'book_id' => $bookId,
                'quantity' => $quantity,
            ]);
        }

        return $this->successResponse($cartItem->load('book'), 'تمت إضافة المنتج إلى السلة بنجاح');
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Request $request, $id): JsonResponse
    {
        $cartItem = CartItem::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$cartItem) {
            return $this->errorResponse('العنصر غير موجود في السلة', 404);
        }

        $cartItem->delete();

        return $this->successResponse(null, 'تم حذف المنتج من السلة');
    }

    /**
     * Clear user cart
     */
    public function clearCart(Request $request): JsonResponse
    {
        CartItem::where('user_id', $request->user()->id)->delete();

        return $this->successResponse(null, 'تم تفريغ السلة بنجاح');
    }
}

