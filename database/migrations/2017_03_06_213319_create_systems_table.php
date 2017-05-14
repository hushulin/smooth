<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('systems', function (Blueprint $table) {
      $table->increments('id');
      $table->string('convert_max', 5);
      $table->string('convert_min', 5);
      $table->integer('interest_rate');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::drop('systems');
  }
}
