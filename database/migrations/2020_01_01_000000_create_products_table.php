<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use SpaceCode\Maia\Models\Product;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id');
            $table->string('title');
            $table->string('guard_name');
            $table->text('excerpt')->nullable();
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->decimal('amount', 12, 0)->default(0);
            $table->decimal('wholesale_from', 12, 0)->nullable();
            $table->decimal('regular_price', 12, 2);
            $table->decimal('wholesale_price', 12, 2)->nullable();
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->decimal('discount_wholesale_price', 12, 2)->nullable();
            $table->timestamp('discount_date_from')->nullable();
            $table->timestamp('discount_date_to')->nullable();
            $table->timestamp('discount_wholesale_date_from')->nullable();
            $table->timestamp('discount_wholesale_date_to')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('json_ld')->nullable();
            $table->text('open_graph')->nullable();
            $table->enum('document_state', Product::$states)->default(Product::STATE_DYNAMIC);
            $table->text('index')->nullable();
            $table->string('comments')->default(0);
            $table->enum('status', Product::$statuses)->default(Product::STATUS_PENDING);
            $table->string('template')->default('default');
            $table->string('view_unique')->nullable();
            $table->string('view')->nullable();
            $table->string('sales')->nullable();
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
        Schema::dropIfExists('products');
    }
}
