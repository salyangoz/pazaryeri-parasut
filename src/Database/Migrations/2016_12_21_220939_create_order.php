<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('marketplace',['Gittigidiyor','N11','Hepsiburada']);
            $table->string('order_id');
            $table->string('customer_id');
            $table->string('description');
            $table->enum('e_invoice_status',['waiting','request_sent','ready']);
            $table->enum('e_invoice_document_type',['e_archive','e_invoice'])->nullable();
            $table->string('e_invoice_url')->nullable();
            $table->float('amount');
            $table->integer('parasut_id')->nullable();
            $table->dateTime("order_created_at")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime("einvoice_created_at")->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order');
    }
}
