<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_user_sector_permission', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');                      // FK to users.id
            $table->string('sector_id', 50);                            // FK to tbl_ref_sector.sector_id
            $table->string('sub_sector_id', 50)->nullable();            // FK to tbl_ref_sub_sector.sub_sector_id; NULL = all sub-sectors
            $table->timestamps();

            // Ensure no duplicate sector/sub-sector combos per user
            $table->unique(['id_user', 'sector_id', 'sub_sector_id'], 'unique_user_sector_subsector');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_user_sector_permission');
    }
};
