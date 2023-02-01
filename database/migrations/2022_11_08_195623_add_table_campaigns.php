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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->mediumText('description');
            $table->mediumText('image');
            $table->integer('order')->default(0);
            $table->dateTime('deadline_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('campaigns_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categorie_id');
            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('categorie_id')->references('id')->on('categories');
            $table->timestamps();
        });

        Schema::create('campaigns_has_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->timestamps();
        });

        Schema::create('campaigns_has_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('campaign_id');
            $table->foreign('campaign_id')->references('id')->on('campaigns');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('campaigns_has_categories');
        Schema::dropIfExists('campaigns');
    }
};
