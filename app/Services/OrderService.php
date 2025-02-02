<?php

namespace App\Services;

use Illuminate\Http\Response;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderService {
    public function __construct(private Order $order) {}

    /**
     * Lista os pedidos com filtro.
     *
     * @return array
     * @param array $request Campos opcionais para filtrar: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function getOrders(array $request): array
    {
        try {
            $limit = $request['limit'] ?? 100;
            $page = $request['page'] ?? 1;

            if ($limit > 100) {
                $limit = 100;
            }

            $orders = Order::query();

            if (empty($request['user_id']) === false) {
                $orders->where('user_id', $request['user_id']);
            }
            if (empty($request['requester_name']) === false) {
                $orders->where('requester_name', 'like', '%' . $request['requester_name'] . '%');
            }
            if (empty($request['destination_name']) === false) {
                $orders->where('destination_name', 'like', '%' . $request['destination_name'] . '%');
            }
            if (empty($request['departure_date_start']) === false && empty($request['departure_date_end']) === false) {
                $orders->whereBetween('departure_date', [
                    $request['departure_date_start'],
                    $request['departure_date_end']
                ]);
            }
            if (empty($request['return_date_start']) === false && empty($request['return_date_end']) === false) {
                $orders->whereBetween('return_date', [
                    $request['return_date_start'],
                    $request['return_date_end']
                ]);
            }
            if (empty($request['status']) === false) {
                $orders->where('status', $request['status']);
            }

            $paginatedOrders = (array) $orders->paginate($limit, ['*'], 'page', $page);

            return $paginatedOrders;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return [];
        }
    }

    /**
     * Cadastro do pedido de viagem.
     *
     * @return array
     * @param array $request Campos: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function create(array $request): array
    {
        try {

            $order = Order::create($request);

            return (array) $order;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return [];
        }
    }

    /**
     * Pegar um pedido pelo ID.
     *
     * @return array
     * @param string $id ID do pedido.
     */
    public function get(string $id): array
    {
        try {
            $order = Order::findOrFail($id);
            return (array) $order;
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());

            return [];
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return [];
        }
    }

    /**
     * Editar o status do pedido.
     *
     * @return array
     * @param string $id ID do pedido.
     * @param string $status
     */
    public function updateStatus(string $id, string $status): array
    {
        try {
            $order = Order::findOrFail($id);

            if ($status === 'canceled' && $this->canCancel($order) === false)
                return [];

            $order->update(['status' => $status]);

            return $order;
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());

            return [];
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return [];
        }
    }

    /**
     * Valida cancelamento de pedido.
     *
     * @return boolean
     * @param Order $order Pedido.
     */
    public function canCancel(Order $order): bool
    {
        if ($order->status === 'requested') {
            return true;
        }

        if ($order->status === 'approved' && \Carbon\Carbon::parse($order->departure_date)->isFuture() === false) {
            Log::error('Não permitido cancelar este pedido. Pois ele já foi aprovado e a data de embarque já passou.');
            return false;
        }

        return true;
    }
}
