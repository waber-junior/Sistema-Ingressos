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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->mediumText('description')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger("user_id");
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->unsignedBigInteger('ticket_id');
            $table->foreign('ticket_id')->references('id')->on('tickets');
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
        Schema::dropIfExists('ticket_attachments');
        Schema::dropIfExists('tickets');
    }
};
