<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use App\Models\SalesStatus;

class SalesStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SalesStatus::create([
            'name' => 'Pedido',
            'description' => '',
            'color' => '#FCC108'
        ]);
        SalesStatus::create([
            'name' => 'Preparando',
            'description' => '',
            'color' => '#2ECAF0'
        ]);
        SalesStatus::create([
            'name' => 'Listo',
            'description' => '',
            'color' => '#1D8754'
        ]);
        SalesStatus::create([
            'name' => 'Enviado',
            'description' => '',
            'color' => '#6C767E'
        ]);
        SalesStatus::create([
            'name' => 'Entregado',
            'description' => '',
            'color' => '#126EFD'
        ]);
    }
}
