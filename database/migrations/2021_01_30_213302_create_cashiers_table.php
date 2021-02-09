<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cashiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('name')->nullable();
            $table->text('observations')->nullable();
            $table->dateTime('opening')->nullable();
            $table->dateTime('closing')->nullable();
            $table->decimal('opening_amount', 10, 2)->nullable()->default(0);
            $table->decimal('closing_amount', 10, 2)->nullable()->default(0);
            $table->decimal('real_amount', 10, 2)->nullable()->default(0);
            $table->decimal('missing_amount', 10, 2)->nullable()->default(0);
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
        Schema::dropIfExists('cashiers');
    }
}
