<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table, $tableName) {
            if(!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('name');
            }
            if(!Schema::hasColumn('users', 'fullName')) {
                $table->json('fullName')->nullable()->after('email');
            }
            if(!Schema::hasColumn('users', 'apps')) {
                $table->json('apps')->nullable()->after('fullName');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('fullName');
            $table->dropColumn('apps');
        });
    }
}
