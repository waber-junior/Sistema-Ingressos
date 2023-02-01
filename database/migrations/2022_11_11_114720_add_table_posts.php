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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->mediumText('description');
            $table->mediumText('attachment')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('posts_has_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categorie_id');
            $table->unsignedBigInteger('post_id');
            $table->foreign('categorie_id')->references('id')->on('categories');
            $table->foreign('post_id')->references('id')->on('posts');
            $table->timestamps();
        });
        Schema::create('posts_has_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('post_id');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('post_id')->references('id')->on('posts');
            $table->timestamps();
        });
        Schema::create('posts_has_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('post_id')->references('id')->on('posts');
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->mediumText('description');
            $table->boolean('isloved')->default(false);
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts_has_categories');
        Schema::dropIfExists('posts_has_groups');
        Schema::dropIfExists('posts_has_users');
        Schema::dropIfExists('posts');
    }
};
