<?php

namespace App\States\OrderStatus;

use App\Models\Order;
use Carbon\Carbon;
use App\States\OrderStateInterface;

class ApprovedState implements OrderStateInterface
{
    /**
     * Aprova pedido.
     *
     * @param Order $order
     * @throws \Exception
     */
    public function approve(Order $order)
    {
        throw new \Exception("Aprovação não permitida. O status atual já é 'approved'.");
    }

    /**
     * Cancela pedido.
     *
     * @param Order $order
     * @throws \Exception
     */
    public function cancel(Order $order)
    {
        if (Carbon::createFromFormat('d/m/Y', $order->departure_date)->isFuture()) {
            $order->status = 'canceled';
            $order->save();
        } else {
            throw new \Exception("Não é possível cancelar um pedido com data de partida vencida.");
        }
    }
}
