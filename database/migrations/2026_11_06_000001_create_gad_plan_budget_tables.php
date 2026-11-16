<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gad_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->decimal('total_office_budget', 18, 2)->default(0);
            $table->string('status', 50)->default('Encoding process');
            $table->string('status_remarks', 500)->nullable();
            $table->date('status_date')->nullable();
            $table->string('ar_attachment')->nullable();
            $table->string('reviewer_attachment')->nullable();
            $table->string('accomplishment_report')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('gad_plan_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gad_plan_id')->constrained('gad_plans')->cascadeOnDelete();
            $table->string('status', 50);
            $table->string('remarks', 500)->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();
        });

        // Org-focused and Client-focused input form
        Schema::create('gad_plan_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gad_plan_id')->constrained('gad_plans')->cascadeOnDelete();
            $table->string('focused', 50);
            $table->text('gender_issue');
            $table->text('title_description');
            $table->text('supporting_statistics')->nullable();
            $table->text('statistics_source')->nullable();
            $table->string('ppa_sector', 100);
            $table->text('gad_objective');
            $table->text('lgu_program');
            $table->string('activity_category', 100);
            $table->string('other_activity_category')->nullable();
            $table->text('gad_activity');
            $table->date('date_of_implementation');
            $table->text('performance_indicator');
            $table->decimal('moe', 18, 2)->default(0);
            $table->decimal('ps', 18, 2)->default(0);
            $table->decimal('co', 18, 2)->default(0);
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        // Attributed Program input form
        Schema::create('gad_plan_attributed_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gad_plan_id')->constrained('gad_plans')->cascadeOnDelete();
            $table->string('ppa_sector', 100);
            $table->string('title');
            $table->string('checklist', 100);
            $table->decimal('hgdg_score', 8, 2)->default(0);
            $table->decimal('total_annual_budget', 18, 2)->default(0);
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gad_plan_attributed_programs');
        Schema::dropIfExists('gad_plan_entries');
        Schema::dropIfExists('gad_plan_status_logs');
        Schema::dropIfExists('gad_plans');
    }
};

