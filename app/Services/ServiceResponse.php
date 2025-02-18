<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ServiceResponse
{
    // Status da resposta, padrão é "Expectation Failed" (417)
    private int $status = Response::HTTP_EXPECTATION_FAILED;

    // Mensagem padrão
    private string $message = 'Erro na requisição';

    // Atributo para armazenar o erro específico, se houver
    private string $error = '';

    // Dados enviados na resposta
    private array $data = [];

    // Instancia de resource para formatar dados
    private string $resourceClass = '';

    // Se na resposta o $data precisa precisa retornar ou somente os dados formatados
    private bool $data_from_collection = true;

    private object $collectionItem;
    private array $collection = [];

    /**
     * Pega o codigo de erro.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Define codigo de erro.
     *
     * @param int $status
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * Pega a mensagem do usuário.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Define a mensagem para o usuário.
     *
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Pega os dados.
     *
     * @return array
     */
    public function getData(): array
    {
        if ($this->getDataFromCollection() === true) {
            if (empty($this->collectionItem) === false) {
                return $this->getCollectionItem();
            } elseif (empty($this->collection) === false) {
                return $this->getCollection();
            }
        }

        return $this->data;
    }

    /**
     * Define os dados.
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Mensagem de erro na requisição.
     *
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * Mensagem de erro.
     *
     * @param string $error
     * @return void
     */
    public function setError(string $error): void
    {
        Log::error($error);
        $this->error = $error;
    }

    /**
     * Retornar collection com dados formatados.
     *
     * @return bool
     */
    public function getDataFromCollection(): bool
    {
        return $this->data_from_collection;
    }

    /**
     * Se é pra retornar collection com dados formatados.
     *
     * @param bool $data_from_collection
     * @return void
     */
    public function setDataFromCollection(bool $data_from_collection): void
    {
        $this->data_from_collection = $data_from_collection;
    }

    /**
     * Configura dados formatados do resource.
     *
     * @param JsonResource $resource
     * @return void
     */
    public function setResource(string $resourceClass): void
    {
        if (is_subclass_of($resourceClass, JsonResource::class) === false) {
            $this->setError("O recurso deve ser uma instância de JsonResource.");
        }

        $this->resourceClass = $resourceClass;
    }

    /**
     * Configura uma lista. Ex: lista de usuarios e tarefas.
     *
     * @param JsonResource $resource
     * @return void
     */
    public function setCollection(array $collection): void
    {
        $this->collection = $collection;
    }

    /**
     * Configura item. Ex: objeto da classe User e Task.
     *
     * @param JsonResource $resource
     * @return void
     */
    public function setCollectionItem(object $collectionItem): void
    {
        $this->collectionItem = $collectionItem;
    }

    /**
     * Pega um item.
     *
     * @param JsonResource $resource
     * @return void
     */
    public function getCollectionItem(): array
    {
        if ($this->validateResource() === false) return [];

        $resourceCollection = new $this->resourceClass($this->collectionItem);
        $this->data = $resourceCollection->resolve();
        return $this->data;
    }

    /**
     * Pega uma lista. Ex: lista de usuarios ou tarefas.
     *
     * @param JsonResource $resource
     * @return void
     */
    public function getCollection(): array
    {
        if ($this->validateResource() === false) return [];

        $resourceCollection = $this->resourceClass::collection((array) $this->collection);
        $this->data = $resourceCollection->resolve();
        return $this->data;
    }

    /**
     * Valida que foi configurado o resource. Ex: UserResource ou TaskResource.
     * Pois o getCollectionItem e getCollection precisa do resource para formatar os dados.
     *
     * @return bool
     */
    public function validateResource(): bool
    {
        if (
            empty($this->resourceClass) === false &&
            is_subclass_of($this->resourceClass, JsonResource::class
        ) === true) {
            return true;
        }

        return false;
    }

    /**
     * Retornar resposta.
     *
     * @return array
     */
    public function getResponse(): array
    {
        $count = 0;

        if (empty($this->collection) === false) {
            $count = count($this->getData());
        } elseif (empty($this->collectionItem) === false) {
            $count = 1;
        }

        return [
            'message' => $this->getMessage(),
            'data' => $this->getData(),
            'count' => $count,
            'error' => $this->getError(),
            'status' => $this->getStatus()
        ];
    }

    /**
     * Retornar resposta em Json.
     *
     * @return JsonResponse
     */
    public function getJsonResponse(): JsonResponse
    {
        $data = $this->getResponse();
        return response()->json($data, $data['status']);
    }
}
