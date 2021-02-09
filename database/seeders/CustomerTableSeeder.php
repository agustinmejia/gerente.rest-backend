<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use App\Models\Customer;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'person_id' => 1
        ]);
    }
}
