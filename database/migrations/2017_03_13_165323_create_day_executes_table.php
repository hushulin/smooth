<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayExecutesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('day_executes', function (Blueprint $table) {
      $table->increments('id');
      $table->string('start', 20)->comment('开始执行时间');
      $table->string('end', 20)->comment('结束执行时间');
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
    Schema::drop('day_executes');
  }
}
