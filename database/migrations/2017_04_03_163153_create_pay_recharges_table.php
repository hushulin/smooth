<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayRechargesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('pay_recharges', function (Blueprint $table) {
      $table->increments('id');
      $table->string('id_user', 20);
      $table->string('order_sn', 50);
      $table->string('total', 10);
      $table->string('times', 20);
    });
  }
  
  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('pay_recharges');
  }
}
