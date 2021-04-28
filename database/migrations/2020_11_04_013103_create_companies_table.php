<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('owners');
            $table->string('name')->nullable();
            $table->string('slug')->unique();
            $table->string('slogan')->nullable();
            $table->string('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->foreignId('city_id')->nullable()->constrained();
            $table->string('address')->nullable();
            $table->string('phones')->nullable();
            $table->string('logos')->nullable();
            $table->string('banners')->nullable();
            $table->string('nit')->nullable();
            $table->string('activity_description')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('companies');
    }
}
