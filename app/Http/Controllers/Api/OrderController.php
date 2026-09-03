<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CheckoutRequest;
use App\Models\Book;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    use ApiResponseTrait;

    /**
     * Checkout / Create new order
     */
    public function checkout(CheckoutRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user('sanctum');
        $orderItemsData = [];
        $subtotal = 0;

        // Process items: Either from payload items or from user's Cart
        if (!empty($validated['items']) && count($validated['items']) > 0) {
            foreach ($validated['items'] as $item) {
                $book = Book::find($item['book_id']);
                if (!$book || !$book->is_active) {
                    return $this->errorResponse("الكتاب ({$item['book_id']}) غير متوفر حالياً", 400);
                }
                $qty = $item['quantity'];
                $lineTotal = $book->price * $qty;
                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'price' => $book->price,
                    'quantity' => $qty,
                    'total' => $lineTotal,
                ];
            }
        } elseif ($user) {
            $cartItems = CartItem::where('user_id', $user->id)->with('book')->get();
            if ($cartItems->isEmpty()) {
                return $this->errorResponse('سلة التسوق فارغة', 400);
            }

            foreach ($cartItems as $cartItem) {
                $book = $cartItem->book;
                $qty = $cartItem->quantity;
                $lineTotal = $book->price * $qty;
                $subtotal += $lineTotal;

                $orderItemsData[] = [
                    'book_id' => $book->id,
                    'book_title' => $book->title,
                    'price' => $book->price,
                    'quantity' => $qty,
                    'total' => $lineTotal,
                ];
            }
        } else {
            return $this->errorResponse('الرجاء تزويد عناصر الطلب أو تسجيل الدخول لاستخدام السلة', 400);
        }

        // Calculate shipping & total (Free shipping if subtotal >= 699)
        $shippingFee = ($subtotal >= 699) ? 0 : 50.00;
        $total = $subtotal + $shippingFee;

        $orderNumber = 'ORD-' . strtoupper(Str::random(4)) . '-' . date('YmdHis');

        return DB::transaction(function () use ($request, $user, $orderNumber, $subtotal, $shippingFee, $total, $orderItemsData) {
            $order = Order::create([
                'user_id' => $user?->id,
                'order_number' => $orderNumber,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email ?? $user?->email,
                'phone' => $request->phone,
                'city' => $request->city,
                'full_address' => $request->full_address,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'discount' => 0,
                'shipping_fee' => $shippingFee,
                'total' => $total,
                'payment_method' => $request->get('payment_method', 'cash_on_delivery'),
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            // Clear cart if user is logged in
            if ($user) {
                CartItem::where('user_id', $user->id)->delete();
            }

            return $this->successResponse($order->load('items'), 'تم تأكيد الطلب بنجاح', 201);
        });
    }

    /**
     * Get authenticated user orders list
     */
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with('items')
            ->latest()
            ->paginate(10);

        return $this->successResponse($orders, 'تم جلب قائمة الطلبات بنجاح');
    }

    /**
     * Get specific order details
     */
    public function show(Request $request, $id): JsonResponse
    {
        $order = Order::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->with('items.book')
            ->first();

        if (!$order) {
            return $this->errorResponse('الطلب غير موجود', 404);
        }

        return $this->successResponse($order, 'تم جلب تفاصيل الطلب');
    }

    /**
     * Track order by order number (Public for guests & users)
     */
    public function trackOrder($orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items')
            ->first();

        if (!$order) {
            return $this->errorResponse('رقم الطلب غير صحيح أو غير موجود', 404);
        }

        return $this->successResponse([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'total' => $order->total,
            'created_at' => $order->created_at->format('Y-m-d H:i'),
            'items_count' => $order->items->count(),
            'items' => $order->items,
        ], 'تم جلب حالة الطلب بنجاح');
    }
}
