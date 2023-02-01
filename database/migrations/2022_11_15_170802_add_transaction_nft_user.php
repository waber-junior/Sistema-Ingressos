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
        Schema::table('nfts', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('nft_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', ['PENDING', 'ACCEPTED', "REJECTED"])->default('PENDING');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('recipient_id');
            $table->unsignedBigInteger('nft_id');
            $table->foreign('nft_id')->references('id')->on('nfts');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('recipient_id')->references('id')->on('users');
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
        Schema::dropIfExists('nfts_users');
    }
};
