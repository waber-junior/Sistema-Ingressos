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
        Schema::create('nft_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('nft_classifications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('nfts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->mediumText('image')->nullable();
            $table->mediumText('description');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'SUSPEND'])->default('ACTIVE');
            $table->timestamps();
        });

        Schema::create('nfts_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nft_categorie_id');
            $table->unsignedBigInteger('nft_id');
            $table->foreign('nft_categorie_id')->references('id')->on('nft_categories');
            $table->foreign('nft_id')->references('id')->on('nfts');
            $table->timestamps();
        });

        Schema::create('nfts_has_classifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nft_classification_id');
            $table->unsignedBigInteger('nft_id');
            $table->foreign('nft_classification_id')->references('id')->on('nft_classifications');
            $table->foreign('nft_id')->references('id')->on('nfts');
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
        Schema::dropIfExists('nfts');
        Schema::dropIfExists('nft_categories');
        Schema::dropIfExists('nft_classifications');
    }
};
