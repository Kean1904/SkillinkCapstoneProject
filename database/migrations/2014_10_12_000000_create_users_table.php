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
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->enum('role', ['skilled worker', 'residential', 'peso staff', 'admin']);
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('location_tag')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('date_created')->useCurrent();
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
        Schema::dropIfExists('users');
    }
};
