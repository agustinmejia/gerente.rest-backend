<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            UsersTableSeeder::class,
            RolesTableSeeder::class,
            CitiesTableSeeder::class,
            ProductCategoriesTableSeeder::class,
            PeopleTableSeeder::class,
            CustomerTableSeeder::class,
            SuscriptionsTypeTableSeeder::class,
            SalesStatusTableSeeder::class,
            CompaniesTypesTableSeeder::class
        ]);
    }
}
