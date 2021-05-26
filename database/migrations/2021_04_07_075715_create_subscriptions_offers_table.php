<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriptions_type_id')->nullable()->constrained('subscriptions_types');
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->date('start')->nullable();
            $table->date('end')->nullable();
            $table->smallInteger('status')->nullable()->default(1);
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
        Schema::dropIfExists('subscriptions_offers');
    }
}
