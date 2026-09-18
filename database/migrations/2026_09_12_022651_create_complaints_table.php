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
       Schema::create('complaints', function (Blueprint $table) {
            $table->id('complaint_id');
            $table->foreignId('booking_id')->constrained('bookings', 'booking_id')->onDelete('cascade');
            $table->foreignId('submitted_by')->constrained('users', 'user_id')->onDelete('cascade');
            $table->text('description');
            $table->string('complaint_type');
            $table->string('status')->default('pending');
            $table->text('resolution_notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaints');
    }
};
