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
        Schema::create('worker_profiles', function (Blueprint $table) {
            $table->id('worker_id');
            $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->string('skill_tags')->nullable();
            $table->text('biography')->nullable();
            $table->string('service_categories')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->string('availability_status')->default('available');
            $table->integer('experience_years')->default(0);
            $table->string('profile_image')->nullable();
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
        Schema::dropIfExists('worker_profiles');
    }
};
