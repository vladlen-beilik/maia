<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use SpaceCode\Maia\Models\Shop;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('author_id');
            $table->string('name');
            $table->string('guard_name');
            $table->text('excerpt')->nullable();
            $table->string('logo')->nullable();
            $table->string('slug')->unique();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('json_ld')->nullable();
            $table->text('open_graph')->nullable();
            $table->enum('document_state', Shop::$states)->default(Shop::STATE_DYNAMIC);
            $table->text('index')->nullable();
            $table->enum('status', Shop::$statuses)->default(Shop::STATUS_PENDING);
            $table->string('template')->default('default');
            $table->string('view_unique')->nullable();
            $table->string('view')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('shops_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key');
            $table->longText('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
        Schema::dropIfExists('shops_meta');
    }
}
