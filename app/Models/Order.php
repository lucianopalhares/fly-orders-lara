<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'requester_name',
        'destination_name',
        'departure_date',
        'return_date',
        'status'
    ];

    protected $dates = ['created_at', 'departure_date', 'return_date'];

    /**
     * Obtém a data de criação formatada no formato 'd/m/Y'.
     *
     * @param string $value O valor da data de criação.
     * @return string A data formatada.
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    /**
     * Obtém a data de partida formatada no formato 'd/m/Y'.
     *
     * @param string $value O valor da data de partida.
     * @return string A data formatada.
     */
    public function getDepartureDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    /**
     * Obtém a data de retorno formatada no formato 'd/m/Y'.
     * Se o valor for nulo, retorna null.
     *
     * @param string|null $value O valor da data de retorno (pode ser nulo).
     * @return string|null A data formatada ou null caso o valor seja nulo.
     */
    public function getReturnDateAttribute($value)
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : null;
    }

    /**
     * Retorna o usuário solicitante dono do pedido.
     *
     * @return object Objeto da classe User
     */
    public function user(): object {
        return $this->belongsTo(User::class);
    }
}
