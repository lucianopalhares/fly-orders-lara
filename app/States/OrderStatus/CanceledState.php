<?php

namespace App\States\OrderStatus;

use App\Models\Order;
use App\States\OrderStateInterface;

class CanceledState implements OrderStateInterface
{
    /**
     * Tenta aprovar um pedido cancelado.
     *
     * @param Order $order
     * @throws \Exception
     */
    public function approve(Order $order)
    {
        throw new \Exception("Não é possível aprovar um pedido cancelado.");
    }

    /**
     * Tenta cancelar um pedido já cancelado.
     *
     * @param Order $order
     * @throws \Exception
     */
    public function cancel(Order $order)
    {
        throw new \Exception("Este pedido já está cancelado.");
    }
}
