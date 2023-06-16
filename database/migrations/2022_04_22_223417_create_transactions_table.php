<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('product_id')->nullable()->references('id')->on('products')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignUuid('bank_id')->nullable()->nullable()->references('id')->on('banks')->cascadeOnUpdate()->nullOnDelete();
            $table->string('invoice')->unique();
            $table->unsignedInteger('payment_code')->default(0);
            $table->unsignedBigInteger('fee')->default(0);
            $table->unsignedBigInteger('grand_total');
            $table->json('product');
            $table->json('coupon')->nullable();
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->text('confirmation')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->foreignUuid('edited_by')->nullable()->references('id')->on('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('edited_at')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
