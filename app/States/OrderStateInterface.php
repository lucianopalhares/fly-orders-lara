<?php

namespace App\States;

use App\Models\Order;

interface OrderStateInterface
{
    /**
     * Aprova pedido.
     *
     * @param Order $order
     * @throws \Exception
     */
    public function approve(Order $order);

    /**
     * Tenta cancelar um pedido.
     *
     * @param Order $order
     * @throws \Exception
     */
    public function cancel(Order $order);
}
