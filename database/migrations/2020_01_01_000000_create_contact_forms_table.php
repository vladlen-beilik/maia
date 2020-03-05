<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_forms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sender')->nullable();
            $table->string('title')->nullable();
            $table->string('contacts')->nullable();
            $table->string('description')->nullable();
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
        Schema::dropIfExists('contact_forms');
    }
}
