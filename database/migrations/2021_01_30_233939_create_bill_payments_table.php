<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('bill_number')->nullable();
            $table->string('control_code')->nullable();
            $table->foreignId('bill_dosage_id')->nullable()->constrained('bill_dosages');
            $table->string('status', 1)->nullable();
            $table->decimal('amount', 10, 2)->nullable()->default(0);
            $table->decimal('amount_ice', 10, 2)->nullable()->default(0);
            $table->decimal('amount_exempt', 10, 2)->nullable()->default(0);
            $table->decimal('zero_rate', 10, 2)->nullable()->default(0);
            $table->decimal('subtotal', 10, 2)->nullable()->default(0);
            $table->decimal('discount', 10, 2)->nullable()->default(0);
            $table->decimal('base_amount', 10, 2)->nullable()->default(0);
            $table->decimal('fiscal_debit', 10, 2)->nullable()->default(0);
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
        Schema::dropIfExists('bill_payments');
    }
}
