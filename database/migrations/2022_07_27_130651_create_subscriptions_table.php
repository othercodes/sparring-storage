<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')
                ->references('id')
                ->on('accounts')
                ->onDelete('cascade');
            $table->string('plan_sku');
            $table->foreign('plan_sku')
                ->references('sku')
                ->on('plans')
                ->onDelete('cascade');
            $table->integer('quantity');
            $table->string('status');
            $table->timestamp('renewal_at');
            $table->timestamp('subscribed_at');
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
        Schema::dropIfExists('subscriptions');
    }
};
