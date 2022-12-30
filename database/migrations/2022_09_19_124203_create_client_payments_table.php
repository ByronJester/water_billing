<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('client_id')->unsigned()->comment('Foreign key from table clients'); 
            $table->integer('consumed_cubic_meter');
            $table->double('amount', 8, 2);
            $table->double('payment', 8, 2)->default(0);
            $table->double('penalty', 8, 2)->default(0);
            $table->double('penalty_payment', 8, 2)->default(0);
            $table->date('date');
            $table->string('status');
            $table->date('payment_date')->nullable();
            $table->string('receipt')->nullable();
            
            $table->foreign('client_id')->references('id')->on('clients');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_payments');
    }
}
