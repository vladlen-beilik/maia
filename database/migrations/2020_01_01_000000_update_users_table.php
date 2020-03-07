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
        $tableName = 'users';
        Schema::table($tableName, function (Blueprint $table, $tableName) {
            if(!Schema::hasColumn($tableName, 'avatar')) {
                $table->string('avatar')->nullable()->after('name');
            }
            if(!Schema::hasColumn($tableName, 'fullName')) {
                $table->json('fullName')->nullable()->after('email');
            }
            if(!Schema::hasColumn($tableName, 'apps')) {
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
