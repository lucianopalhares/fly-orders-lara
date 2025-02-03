<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OrderFactory extends Factory
{
    /**
     * O nome da tabela associada ao modelo.
     *
     * @var string
     */
    protected $model = \App\Models\Order::class;

    /**
     * Defina os atributos do modelo.
     *
     * @return array
     */
    public function definition()
    {

        $returnDate = $this->faker->optional()->dateTimeBetween('+1 year', '+2 years');

        return [
            'user_id' => User::factory(), // Criando um usuário associado para a ordem
            'requester_name' => $this->faker->name,
            'destination_name' => $this->faker->company,
            'departure_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'return_date' => $returnDate ? $returnDate->format('Y-m-d') : null,
            'status' => 'requested'
        ];
    }
}
