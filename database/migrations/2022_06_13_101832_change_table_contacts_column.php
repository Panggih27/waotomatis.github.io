<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTableContactsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
            $table->dropColumn(['tag_id']);
            $table->string('var1')->nullable()->after('number');
            $table->string('var2')->nullable()->after('var1');
            $table->string('var3')->nullable()->after('var2');
            $table->string('var4')->nullable()->after('var3');
            $table->string('var5')->nullable()->after('var4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
}
