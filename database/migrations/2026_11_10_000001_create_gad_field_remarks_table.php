<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gad_field_remarks', function (Blueprint $table) {
            $table->id();
            // Morph relation: GadPlanEntry, GadAccomplishmentEntry, etc.
            $table->string('remarkable_type');
            $table->unsignedBigInteger('remarkable_id');
            // The specific field name (e.g. 'gender_issue', 'gad_objective', 'lgu_program', 'gad_activity', 'performance_indicator', 'moe', 'ps', 'co', 'title', 'total_annual_budget')
            $table->string('field_name', 100);
            $table->text('remarks');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();

            $table->index(['remarkable_type', 'remarkable_id', 'field_name'], 'idx_remarkable_field');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gad_field_remarks');
    }
};

