<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTableOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->string('name')->unique();;
            $table->string('phoneNumber')->unique();;
            $table->string('address')->unique();;
            $table->string('city')->unique();;
            $table->string('district')->unique();;
            $table->string('ward')->unique();;
            $table->string('addressDetail')->unique();;
            $table->string('email')->unique();;


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
