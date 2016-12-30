<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('marketplace',['Gittigidiyor','N11','Hepsiburada']);
            $table->string('customer_id');
            $table->enum('type',['Customer','Company']);
            $table->string('name');
            $table->string('invoice_address')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->bigInteger('tc')->nullable()->default('11111111111');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->bigInteger('tax_number')->nullable();
            $table->string('tax_office')->nullable();
            $table->integer('parasut_id')->nullable();
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
        Schema::drop('customer');
    }
}
