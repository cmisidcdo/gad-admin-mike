<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gad_accomplishments', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->decimal('total_office_budget', 18, 2)->default(0);
            $table->string('status', 50)->default('Encoding process');
            $table->string('status_remarks', 500)->nullable();
            $table->date('status_date')->nullable();
            $table->string('ar_attachment')->nullable();
            $table->string('reviewer_attachment')->nullable();
            $table->unsignedBigInteger('attached_to_gpb_id')->nullable(); // linked GPB plan
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('gad_accomplishment_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gad_accomplishment_id');
            $table->string('status', 50);
            $table->string('remarks', 500)->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('gad_accomplishment_id', 'fk_gad_ar_logs_arid')
                  ->references('id')->on('gad_accomplishments')->cascadeOnDelete();
        });

        // AR Org-focused and Client-focused entries
        Schema::create('gad_accomplishment_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gad_accomplishment_id');
            $table->string('focus', 50)->nullable();
            $table->text('gender_issue');
            $table->text('gad_objective');
            $table->text('lgu_program');
            $table->text('gad_activity');
            $table->text('performance_indicator');
            $table->text('actual_results')->nullable();
            $table->decimal('approved_gad_budget', 18, 2)->default(0);
            $table->decimal('actual_cost', 18, 2)->default(0);
            $table->text('variance_remarks')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->foreign('gad_accomplishment_id', 'fk_gad_ar_entries_arid')
                  ->references('id')->on('gad_accomplishments')->cascadeOnDelete();
        });

        // AR Attributed Program entries
        Schema::create('gad_accomplishment_attributed_programs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gad_accomplishment_id');
            $table->string('title');
            $table->string('checklist_score_desc')->nullable();
            $table->decimal('hgdg_score', 8, 2)->default(0);
            $table->decimal('total_annual_cost', 18, 2)->default(0);
            $table->decimal('gad_attributed_cost', 18, 2)->default(0);
            $table->text('variance_remarks')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->foreign('gad_accomplishment_id', 'fk_gad_ar_attrib_arid')
                  ->references('id')->on('gad_accomplishments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gad_accomplishment_attributed_programs');
        Schema::dropIfExists('gad_accomplishment_entries');
        Schema::dropIfExists('gad_accomplishment_status_logs');
        Schema::dropIfExists('gad_accomplishments');
    }
};
