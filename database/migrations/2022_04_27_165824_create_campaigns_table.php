<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->foreignUuid('number_id')->references('id')->on('numbers');
            $table->string('title');
            $table->string('slug');
            $table->json('receivers')->nullable();
            $table->unsignedInteger('point');
            $table->boolean('is_manual')->default(false);
            $table->boolean('is_processing')->default(false);
            $table->dateTime('schedule')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('executed_at')->nullable();
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
        Schema::dropIfExists('campaigns');
    }
}
