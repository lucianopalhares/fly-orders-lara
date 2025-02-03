<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Criar uma nova instância de notificação.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Define os canais de notificação.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Define a estrutura da notificação no banco de dados.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => "O status do seu pedido #{$this->order->id} foi alterado para: {$this->order->status}",
        ];
    }
}
