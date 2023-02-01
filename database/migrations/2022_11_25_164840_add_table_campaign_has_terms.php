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
        Schema::create('campaigns_has_terms', function (Blueprint $table) {
            $table->unsignedBigInteger('term_id');
            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('term_id')->references('id')->on('terms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns_has_terms');
    }
};
