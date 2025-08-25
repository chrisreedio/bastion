<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $tableNames = config('permission.table_names');

        // Add resource column to permissions
        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('resource')->nullable()->after('id');
        });

        // Add display_name column to permissions
        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('name');
        });

        // Add sso_group column to roles
        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->string('sso_group')->nullable()->after('name');
        });
    }

    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropColumn(['resource', 'display_name']);
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropColumn('sso_group');
        });
    }
};
