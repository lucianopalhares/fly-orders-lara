<?php

namespace App\States;

use App\States\OrderStatus\RequestedState;
use App\States\OrderStatus\ApprovedState;
use App\States\OrderStatus\CanceledState;
use Exception;

class OrderStateFactory
{
    /**
     * Cria uma instância do estado da ordem com base no status informado.
     *
     * @param string $status O status da ordem ('requested', 'approved' ou 'canceled').
     * @return mixed Retorna uma instância do estado correspondente.
     * @throws Exception Se o status informado for inválido.
     */
    public static function create(string $status)
    {
        switch ($status) {
            case 'requested':
                return new RequestedState();
            case 'approved':
                return new ApprovedState();
            case 'canceled':
                return new CanceledState();
            default:
                throw new Exception("Estado inválido.");
        }
    }
}
