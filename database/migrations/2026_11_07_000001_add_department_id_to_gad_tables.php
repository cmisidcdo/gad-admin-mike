<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gad_plans', function (Blueprint $table) {
            $table->string('department_id', 50)->nullable()->after('created_by');
        });

        Schema::table('gad_accomplishments', function (Blueprint $table) {
            $table->string('department_id', 50)->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('gad_plans', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });

        Schema::table('gad_accomplishments', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
    }
};

