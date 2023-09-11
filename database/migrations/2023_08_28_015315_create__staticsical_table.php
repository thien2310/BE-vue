<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticsicalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staticsical', function (Blueprint $table) {
            $table->id();
            $table->string('order_date')->unique();
            $table->integer('sales');   //doanh thu
            $table->integer('profit');  //lợi nhuận
            $table->integer('quantity');    // số lượng sản phẩm bán ra
            $table->integer('total_order'); // tổng số đơn bán ra
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
        Schema::dropIfExists('_staticsical');
    }
}
