<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use App\Models\Person;

class PeopleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Person::create([
            'first_name' => 'Sin nombre',
        ]);
    }
}
