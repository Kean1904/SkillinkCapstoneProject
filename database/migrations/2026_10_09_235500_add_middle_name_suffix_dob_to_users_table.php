<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'suffix')) {
                $table->string('suffix', 50)->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('age');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'middle_name')) $cols[] = 'middle_name';
            if (Schema::hasColumn('users', 'suffix')) $cols[] = 'suffix';
            if (Schema::hasColumn('users', 'date_of_birth')) $cols[] = 'date_of_birth';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
