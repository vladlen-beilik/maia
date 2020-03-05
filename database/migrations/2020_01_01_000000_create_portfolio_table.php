<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use SpaceCode\Maia\Models\Portfolio;

class CreatePortfolioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('author_id');
            $table->string('title');
            $table->string('guard_name');
            $table->text('excerpt')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('json_ld')->nullable();
            $table->text('open_graph')->nullable();
            $table->enum('document_state', Portfolio::$states)->default(Portfolio::STATE_DYNAMIC);
            $table->text('index')->nullable();
            $table->enum('status', Portfolio::$statuses)->default(Portfolio::STATUS_PENDING);
            $table->string('template')->default('default');
            $table->string('view_unique')->nullable();
            $table->string('view')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('portfolio');
    }
}
