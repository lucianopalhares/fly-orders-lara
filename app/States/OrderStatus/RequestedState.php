<?php

namespace App\States\OrderStatus;

use App\Models\Order;
use App\States\OrderStateInterface;

class RequestedState implements OrderStateInterface
{
    /**
     * Aprova um pedido no estado "requested".
     *
     * @param Order $order
     */
    public function approve(Order $order)
    {
        $order->status = 'approved';
        $order->save();
    }

    /**
     * Cancela um pedido no estado "requested".
     *
     * @param Order $order
     */
    public function cancel(Order $order)
    {
        $order->status = 'canceled';
        $order->save();
    }
}
