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

        Schema::create('florals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('sender_id');
            $table->enum('type', ['INPUT', 'OUTPUT']);
            $table->enum('status', ['PENDING', 'ACCEPTED', "REJECTED"])->default('PENDING');
            $table->string('observation')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->unsignedBigInteger('recipient_id');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('recipient_id')->references('id')->on('users');
            $table->timestamps();
            $table->dateTime('accepted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('florals');
    }
};
