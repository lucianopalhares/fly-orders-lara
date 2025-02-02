<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Http\Controllers\Controller;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(private OrderService $service) {}

    /**
     * Lista os pedidos com filtro.
     *
     * @method get
     * @param Request $request Campos opcionais para filtrar: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $orders = $this->service->getOrders($request->all());

            return response()->json($orders);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao recuperar os pedidos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cadastro do pedido de viagem.
     *
     * @method post
     * @param Request $request Campos: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'requester_name' => 'required|string',
                'destination_name' => 'required|string',
                'departure_date' => 'required|date',
                'return_date' => 'nullable|date',
                'status' => 'required|in:requested,approved,canceled',
            ]);

            $order = $this->service->create($validated);

            return response()->json($order, 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao criar o pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Pegar um pedido pelo ID.
     *
     * @method get
     * @param string $id ID do pedido.
     */
    public function get(string $id): JsonResponse
    {
        try {
            $order = $this->service->get($id);
            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Pedido não encontrado',
                'message' => 'Pedido com o ID ' . $id . ' não foi encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao recuperar o pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Editar o status do pedido.
     *
     * @method patch
     * @param string $id ID do pedido.
     */
    public function updateStatus(string $id, Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:requested,approved,canceled',
            ]);

            $order = $this->service->get($id, $validated['status']);

            return response()->json($order);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Pedido não encontrado',
                'message' => 'Pedido com o ID ' . $id . ' não foi encontrado'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Falha ao atualizar o status do pedido',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
