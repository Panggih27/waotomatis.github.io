<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAutorepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('autoreplies', function (Blueprint $table) {
            $table->foreignUuid('number_id')->after('user_id')->references('id')->on('numbers')->onDelete('cascade');
            $table->string('search_type')->default('contains')->after('keyword');
            $table->string('reply_type')->default('text')->after('reply');
            $table->dropColumn(['type', 'device']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('autoreplies', function (Blueprint $table) {
            //
        });
    }
}
