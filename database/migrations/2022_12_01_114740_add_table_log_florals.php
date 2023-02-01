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
        Schema::create('florals_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('floral_id');
            $table->string('action');
            $table->string('datails');
            $table->timestamps();
        });
        Schema::create('nft_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('nft_id');
            $table->string('action');
            $table->string('datails');
            $table->timestamps();
        });
        Schema::table('florals', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('nfts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('nft_users', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nft_transactions');
        Schema::dropIfExists('florals_transactions');
        Schema::table('florals', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('nfts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('nft_users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
