<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use App\Models\CompaniesType;

class CompaniesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CompaniesType::create([
            'name' => 'Otro',
            'plural_name' => 'Otros',
            'status' => 1
        ]);
    }
}
