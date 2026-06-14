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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('id_schedules');
            $table->unsignedBigInteger('field_id');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status_schedules', ['available', 'booked', 'closed'])->default('available');
            $table->timestamps();
 
            $table->foreign('field_id')->references('id_fields')->on('fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
