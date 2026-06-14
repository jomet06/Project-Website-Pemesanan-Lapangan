<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('payments', function (Blueprint $table) {
            $table->id('id_payments');
            $table->unsignedBigInteger('booking_id');
            $table->string('midtrans_order_id')->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->nullable();
            $table->enum('status_payments', ['pending', 'success', 'failed', 'expired', 'cancel'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('snap_token')->nullable();
            $table->timestamps();
 
            $table->foreign('booking_id')->references('id_bookings')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
