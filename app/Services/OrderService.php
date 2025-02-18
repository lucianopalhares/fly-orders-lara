<?php

namespace App\Services;

use Illuminate\Http\Response;
use Exception;
use App\Services\ServiceResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Gate;
use App\Notifications\OrderStatusUpdated;
use App\States\OrderStateFactory;

class OrderService extends ServiceResponse {

    public function __construct(private Order $order) {}

    /**
     * Lista os pedidos com filtro.
     *
     * @return bool
     * @param array $request Campos opcionais para filtrar: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function getOrders(array $request): bool
    {
        try {
            $limit = $request['limit'] ?? 100;
            $page = $request['page'] ?? 1;

            if ($limit > 100) {
                $limit = 100;
            }

            $user = request()->user();

            $orders = $user->orders();

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

            $dataPaginated = $orders->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);

            $data = $dataPaginated->map(fn($item) => (object) $item->toArray())->toArray();

            $this->setStatus(Response::HTTP_OK);
            $this->setMessage('Pedidos listados com sucesso!');
            $this->setCollection($data);
            $this->setResource(OrderResource::class);

            return true;
        } catch (Exception $e) {
            $this->setStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setMessage('Erro ao buscar pedidos. Tente novamente mais tarde.');
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * Cadastro do pedido de viagem.
     *
     * @return bool
     * @param array $request Campos: user_id, requester_name, destination_name, departure_date, return_date e status.
     */
    public function create(array $data): bool
    {
        try {
            if (
                empty($data['user_id']) === true ||
                empty($data['requester_name']) === true ||
                empty($data['destination_name']) === true ||
                empty($data['departure_date']) === true ||
                empty($data['return_date']) === true ||
                empty($data['status']) === true
            ) {
                throw new Exception('Os campos nome, email e senha não estão preenchidos!');
            }

            DB::beginTransaction();

            $data = $this->order->create($data);

            Gate::authorize('create', $data);

            $this->setStatus(Response::HTTP_OK);
            $this->setMessage('Pedido cadastrado com sucesso!');
            $this->setCollectionItem($data);
            $this->setResource(OrderResource::class);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollback();

            $this->setStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setMessage('Erro ao cadastrar pedido. Tente novamente mais tarde.');
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * Pegar um pedido pelo ID.
     *
     * @return bool
     * @param string $id ID do pedido.
     */
    public function get(string $id): bool
    {
        try {
            $data = $this->order->findOrFail($id);

            Gate::authorize('view', $data);

            $this->setStatus(Response::HTTP_OK);
            $this->setMessage('Pedido encontrado com sucesso!');
            $this->setCollectionItem($data);
            $this->setResource(OrderResource::class);

            return true;

        } catch (ModelNotFoundException $e) {
            $this->setStatus(Response::HTTP_NOT_FOUND);
            $this->setMessage('Pedido não encontrada.');
            $this->setError($e->getMessage());
        } catch (Exception $e) {
            $this->setStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setMessage('Erro ao encontrar pedido. Tente novamente mais tarde.');
            $this->setError($e->getMessage());
        }

        return false;
    }

    /**
     * Editar o status do pedido.
     *
     * @return bool
     * @param string $id   ID do pedido.
     * @param string $type Tipos suportados: approve, cancel
     */
    public function updateStatus(string $id, string $type): bool
    {
        try {
            $data = $this->order->findOrFail($id);

            DB::beginTransaction();

            Gate::authorize('updateStatus', $data);

            $orderState = OrderStateFactory::create($data->status);

            $orderState->$type($data);

            $this->setStatus(Response::HTTP_OK);
            $this->setMessage('Pedido atualizado com sucesso!');
            $this->setCollectionItem($data);
            $this->setResource(OrderResource::class);

            DB::commit();

            $data->user->notify(new OrderStatusUpdated($data));

            return true;
        } catch (ModelNotFoundException $e) {
            $this->setStatus(Response::HTTP_NOT_FOUND);
            $this->setMessage('Pedido não encontrado.');
            $this->setError($e->getMessage());
        } catch (Exception $e) {
            DB::rollback();

            $this->setStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
            $this->setMessage('Erro ao atualizar status do pedido. Tente novamente mais tarde.');
            $this->setError($e->getMessage());

            return false;
        }
    }

    /**
     * Validar novo pedido.
     *
     * @param Request $request            Dados do pedido para salvar.
     * @param bool    $validateOnlyOrderStatus Se é somente pra validar os status ou todos dados do pedido.
     * @return bool
     */
    function validateOrder(Request $request): bool
    {
        try {
            $orderRequest = new OrderRequest();
            $orderRequest->validate($request);

            return true;
        } catch (ValidationException $e) {
            $this->setStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->setMessage('Verifique os campos do pedido.');
            $this->setError($e->getMessage());

            $errors = $e->errors();

            if (is_array($errors) === true) {
                $errors = $errors;
            } else {
                $errors = json_decode($errors, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    $errors = [];
                }
            }

            $this->setData($errors);

            return false;
        }
    }
}
