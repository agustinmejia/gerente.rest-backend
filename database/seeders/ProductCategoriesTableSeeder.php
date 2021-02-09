<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Models
use App\Models\ProductCategory;

class ProductCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductCategory::create([
            'name' => 'Comidas',
            'description' => ''
        ]);
        ProductCategory::create([
            'name' => 'Bebidas',
            'description' => ''
        ]);
        ProductCategory::create([
            'name' => 'Postres',
            'description' => ''
        ]);
        ProductCategory::create([
            'name' => 'Guarniciones',
            'description' => ''
        ]);
    }
}
