<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use SpaceCode\Maia\Models\Comment;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('author_id')->nullable();
            $table->string('guard_name');
            $table->string('parent_id')->nullable();
            $table->text('body')->nullable();
            $table->enum('status', Comment::$statuses)->default(Comment::STATUS_PENDING);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('comments_relationships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('comment_id');
            $table->bigInteger('item_id');
            $table->string('type');
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
        Schema::dropIfExists('comments_relationships');
    }
}
