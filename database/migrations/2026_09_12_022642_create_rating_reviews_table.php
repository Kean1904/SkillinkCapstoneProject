<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('rating_reviews', function (Blueprint $table) {
            $table->id('rating_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('worker_profiles', 'worker_id')->onDelete('cascade');
            $table->tinyInteger('rating_score');
            $table->text('review_text')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rating_reviews');
    }
};
