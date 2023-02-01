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
        Schema::create('adresses', function (Blueprint $table) {
            $table->id();
            $table->string("street");
            $table->string("number")->nullable();
            $table->string("complement")->nullable();
            $table->string("zipcode")->nullable();
            $table->string("neighborhood")->nullable();
            $table->string("city")->nullable();
            $table->string("state")->nullable();
            $table->timestamps();
        });

        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf')->nullable();
            $table->string('rg')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_whatsapp')->default(false);
            $table->date('birthdate')->nullable();
            
            $table->unsignedBigInteger('addres_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');
            $table->foreign('addres_id')->references('id')->on('adresses');

            $table->dateTime('approved_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable();
            $table->boolean('active')->default(true);
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('active');
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('persons');
        Schema::dropIfExists('adresses');
    }
};
