<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('point');
            $table->unsignedInteger('duration')->comment('by days');
            $table->enum('discount_type', ['percentage', 'amount'])->nullable()->default(null);
            $table->double('discount')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('created_by');
            $table->foreignUuid('updated_by')->nullable();
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
