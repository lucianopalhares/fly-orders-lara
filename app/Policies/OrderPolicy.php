<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determina se o usuário pode visualizar a order.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determina se o usuário pode criar uma order.
     */
    public function create(User $user,  Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    /**
     * Determina se o usuário pode alterar o status da order.
     */
    public function updateStatus(User $user, Order $order): bool
    {
        return $user->id !== $order->user_id;
    }

}
