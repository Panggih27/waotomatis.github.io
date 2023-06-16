<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnTableMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignUuid('campaign_id')->nullable()->after('user_id')->references('id')->on('campaigns')->nullOnDelete();
            $table->dropColumn(['schedule_id', 'message', 'type', 'status']);
            $table->json('body')->nullable()->after('receiver');
            $table->unsignedInteger('point')->nullable()->after('body');
            $table->dateTime('executed_at')->nullable()->after('point');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            //
        });
    }
}
