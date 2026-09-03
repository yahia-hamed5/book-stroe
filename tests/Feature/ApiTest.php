<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_user_can_register_and_get_token(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'أحمد علي',
            'email' => 'ahmed@example.com',
            'password' => 'password123',
            'phone' => '01011112222',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_user_can_login_and_get_token(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'user' => ['id', 'email'],
                    'token',
                ],
            ]);
    }

    public function test_can_fetch_categories_and_books(): void
    {
        $catResponse = $this->getJson('/api/v1/categories');
        $catResponse->assertStatus(200)
            ->assertJsonPath('status', true);

        $bookResponse = $this->getJson('/api/v1/books');
        $bookResponse->assertStatus(200)
            ->assertJsonPath('status', true);

        $searchResponse = $this->getJson('/api/v1/books?search=Flutter');
        $searchResponse->assertStatus(200)
            ->assertJsonPath('status', true);
    }

    public function test_user_can_add_to_cart_and_checkout(): void
    {
        $user = User::where('email', 'user@example.com')->first();
        $book = Book::first();

        // 1. Add item to cart
        $cartResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/cart/items', [
            'book_id' => $book->id,
            'quantity' => 2,
        ]);

        $cartResponse->assertStatus(200)
            ->assertJsonPath('status', true);

        // 2. View cart
        $getCartResponse = $this->actingAs($user, 'sanctum')->getJson('/api/v1/cart');
        $getCartResponse->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.total_items_count', 2);

        // 3. Checkout
        $checkoutResponse = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders/checkout', [
            'first_name' => 'مؤمن',
            'last_name' => 'يحيى',
            'city' => 'القاهرة',
            'full_address' => 'شارع التحرير، الدقي',
            'phone' => '01063888667',
        ]);

        $checkoutResponse->assertStatus(201)
            ->assertJsonPath('status', true)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'order_number',
                    'total',
                    'items',
                ],
            ]);
    }

    public function test_can_fetch_branches_and_features(): void
    {
        $response = $this->getJson('/api/v1/branches');
        $response->assertStatus(200)
            ->assertJsonPath('status', true);

        $featuresResponse = $this->getJson('/api/v1/features');
        $featuresResponse->assertStatus(200)
            ->assertJsonPath('status', true);
    }
}
