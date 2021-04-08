<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use App\Models\SuscriptionsType;

class SuscriptionsTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SuscriptionsType::create([
            'name' => 'Gratuito',
            'description' => 'Utiliza el servicio básico por 30 días a partir de la fecha de registro.',
            'price' => 0
        ]);

        SuscriptionsType::create([
            'name' => 'Básico',
            'description' => '',
            'price' => 200
        ]);

        SuscriptionsType::create([
            'name' => 'Empresarial',
            'description' => '',
            'price' => 300
        ]);
    }
}
