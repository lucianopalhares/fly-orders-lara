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

        if ($passed === true)
            $this->service->create($request->all());

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
     * Editar o status do pedido.
     *
     * @method patch
     * @param string $id ID do pedido.
     */
    public function updateStatus(string $id): JsonResponse
    {
        $this->service->updateStatus($id);
        return $this->service->getJsonResponse();
    }
}
