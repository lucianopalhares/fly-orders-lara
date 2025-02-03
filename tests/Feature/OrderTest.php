<?php

namespace Tests\Feature;

use Tests\TestCase; // Use a classe TestCase do Laravel
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderTest extends TestCase
{
    /**
     * Testar a lista de pedidos na rota /orders/list
     */
    public function test_that_endpoint_orders_returns_a_successful_response(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/orders/list');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'requester_name',
                    'destination_name',
                    'departure_date',
                    'return_date',
                    'status',
                    'created_at'
                ]
            ],
            'count',
            'error',
            'status'
        ]);
    }

    /**
     * Testar a rota de pegar pedido na rota /orders/show/ID
     */
    public function test_that_endpoint_get_a_order_returns_a_successful_response(): void
    {
        $order = Order::factory()->create();

        $token = JWTAuth::fromUser($order->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("/api/orders/show/{$order->id}");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'requester_name',
                'destination_name',
                'departure_date',
                'return_date',
                'status',
                'created_at'
            ],
            'count',
            'error',
            'status'
        ]);
    }

    /**
     * Testar a rota de cadastrar pedido na rota /orders/create
     */
    public function test_that_endpoint_create_a_order_returns_a_successful_response(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/orders/create', [
                "user_id" => $user->id,
                "requester_name" => "João Silva",
                "destination_name" => "Rio de Janeiro",
                "departure_date" => "2025-06-15",
                "return_date" => "2025-08-15",
                "status" => "requested",
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'requester_name',
                'destination_name',
                'departure_date',
                'return_date',
                'status',
                'created_at'
            ],
            'count',
            'error',
            'status'
        ]);
    }

    /**
     * Testar a rota de atualizar proximo statuso do pedido na rota /orders/ID/update-status
     */
    public function test_that_endpoint_update_order_status_returns_a_successful_response(): void
    {
        $order = Order::factory()->create();

        $token = JWTAuth::fromUser($order->user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get("/api/orders/{$order->id}/update-status");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'requester_name',
                'destination_name',
                'departure_date',
                'return_date',
                'status',
                'created_at'
            ],
            'count',
            'error',
            'status'
        ]);
    }

}
