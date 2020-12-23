<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\City;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::create([
            'country' => '',
            'state' => '',
            'name' =>'Otro'
        ]);
        City::create([
            'country' => 'Bolivia',
            'state' => 'Beni',
            'name' =>'Santísima Trinidad'
        ]);
        City::create([
            'country' => 'Bolivia',
            'state' => 'Beni',
            'name' =>'Guayaramerín'
        ]);
        City::create([
            'country' => 'Bolivia',
            'state' => 'Beni',
            'name' =>'Riberalta'
        ]);
    }
}
