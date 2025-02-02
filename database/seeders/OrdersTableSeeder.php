<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Gerar 10 registros falsos para a tabela orders
        for ($i = 0; $i < 10; $i++) {
            $returnDate = $faker->optional()->dateTimeBetween('now', '+1 year');

            DB::table('orders')->insert([
                'user_id' => \App\Models\User::inRandomOrder()->first()->id, // Usuário aleatório
                'requester_name' => $faker->name,
                'destination_name' => $faker->city,
                'departure_date' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                'return_date' => $returnDate ? $returnDate->format('Y-m-d') : null, // Verifique se a data não é nula
                'status' => $faker->randomElement(['requested', 'approved', 'canceled']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
