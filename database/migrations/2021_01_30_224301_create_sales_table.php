<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches');
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('cashier_id')->nullable()->constrained('cashiers');
            $table->integer('sale_number')->nullable();
            $table->smallInteger('payment_type')->nullable()->default(1);
            $table->string('sale_type', 20)->nullable()->default('mesa');
            $table->decimal('total', 10, 2)->nullable()->default(0);
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->smallInteger('paid_out')->nullable()->default(1);
            $table->smallInteger('table_number')->nullable();
            $table->decimal('amount_received', 10, 2)->nullable()->default(0);
            $table->text('observations')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
