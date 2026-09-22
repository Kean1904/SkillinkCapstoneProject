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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'privacy_consent_accepted')) {
                $table->boolean('privacy_consent_accepted')->default(false)->after('status');
            }
            if (!Schema::hasColumn('users', 'privacy_consent_accepted_at')) {
                $table->timestamp('privacy_consent_accepted_at')->nullable()->after('privacy_consent_accepted');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'privacy_consent_accepted_at')) {
                $table->dropColumn('privacy_consent_accepted_at');
            }
            if (Schema::hasColumn('users', 'privacy_consent_accepted')) {
                $table->dropColumn('privacy_consent_accepted');
            }
        });
    }
};
