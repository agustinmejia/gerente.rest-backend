<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies');
            $table->string('name')->nullable();
            $table->string('slug')->unique('slug');
            $table->string('type')->nullable();
            $table->string('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->decimal('price', 10, 2)->nullable()->default(0);
            $table->string('image')->nullable();
            $table->integer('views')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
