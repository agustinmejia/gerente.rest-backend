<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use App\Models\SubscriptionsType;

class SubscriptionsTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubscriptionsType::create([
            'name' => 'Gratuita',
            'description' => 'Utiliza el servicio básico por 30 días a partir de la fecha de registro.',
            'price' => 0,
            'expiration_days' => 30,
            'color' => '#5D6D7E'
        ]);

        SubscriptionsType::create([
            'name' => 'Básica',
            'description' => '',
            'price' => 200,
            'color' => '#28B463'
        ]);

        SubscriptionsType::create([
            'name' => 'Empresarial',
            'description' => '',
            'price' => 300,
            'color' => '#3498DB'
        ]);
    }
}
