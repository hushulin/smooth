<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExecuteLogsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('execute_logs', function (Blueprint $table) {
      $table->increments('id');
      $table->boolean('type')->default(true);
      $table->string('id_wechat', 20);
      $table->string('price', 15);
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
    Schema::drop('execute_logs');
  }
}
