<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Lista os pedidos com filtro.
     *
     * @method get
     * @param Request $request Campos opcionais para filtrar: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function getOrders(Request $request): JsonResponse
    {
        try {
            $limit = $request->input('limit', 100);
            $page = $request->input('page', 1);

            if ($limit > 100) {
                $limit = 100;
            }

            $orders = Order::query();

            if ($request->has('user_id')) {
                $orders->where('user_id', $request->user_id);
            }
            if ($request->has('requester_name')) {
                $orders->where('requester_name', 'like', '%' . $request->requester_name . '%');
            }
            if ($request->has('destination_name')) {
                $orders->where('destination_name', 'like', '%' . $request->destination_name . '%');
            }
            if ($request->has('departure_date_start') && $request->has('departure_date_end')) {
                $orders->whereBetween('departure_date', [
                    $request->departure_date_start,
                    $request->departure_date_end
                ]);
            }

            if ($request->has('return_date_start') && $request->has('return_date_end')) {
                $orders->whereBetween('return_date', [
                    $request->return_date_start,
                    $request->return_date_end
                ]);
            }

            if ($request->has('status')) {
                $orders->where('status', $request->status);
            }

            $paginatedOrders = $orders->paginate($limit, ['*'], 'page', $page);

            return response()->json($paginatedOrders);
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

            $order = Order::create($validated);

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
            $order = Order::findOrFail($id);
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

            $order = Order::findOrFail($id);
            $order->update(['status' => $validated['status']]);

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
