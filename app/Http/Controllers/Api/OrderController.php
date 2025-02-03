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
        $this->service->getOrders($request->all());
        return $this->service->getJsonResponse();
    }

    /**
     * Cadastro do pedido de viagem.
     *
     * @method post
     * @param Request $request Campos: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function create(Request $request): JsonResponse
    {
        $passed = $this->service->validateOrder($request);

        if ($passed === true) {
            $data = $request->all();
            $data['user_id'] = auth('api')->user()->id;
            $this->service->create($data);
        }

        return $this->service->getJsonResponse();
    }

    /**
     * Pegar um pedido pelo ID.
     *
     * @method get
     * @param string $id ID do pedido.
     */
    public function get(string $id): JsonResponse
    {
        $this->service->get($id);
        return $this->service->getJsonResponse();
    }

    /**
     * Cancelar pedido.
     *
     * @method get
     * @param string $id ID do pedido.
     */
    public function cancelOrder(string $id): JsonResponse
    {
        $this->service->updateStatus($id, 'cancel');
        return $this->service->getJsonResponse();
    }

    /**
     * Aprovar pedido.
     *
     * @method get
     * @param string $id ID do pedido.
     */
    public function approveOrder(string $id): JsonResponse
    {
        $this->service->updateStatus($id, 'approve');
        return $this->service->getJsonResponse();
    }
}
