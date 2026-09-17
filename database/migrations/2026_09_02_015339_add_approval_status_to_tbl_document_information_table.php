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
        Schema::table('tbl_document_information', function (Blueprint $table) {
            $table->string('approval_status', 20)->default('pending')->after('is_active');
            $table->string('approved_by', 50)->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->string('decline_reason', 500)->nullable()->after('approved_at');
        });

        // Set all existing documents to 'approved'
        \Illuminate\Support\Facades\DB::table('tbl_document_information')->update([
            'approval_status' => 'approved',
            'approved_by'     => 'System Administrator',
            'approved_at'     => now(),
        ]);

        // Register CMS Menu item if not exists
        $menuExists = \Illuminate\Support\Facades\DB::table('tbl_cms_menu')->where('id', 7)->exists();
        if (!$menuExists) {
            \Illuminate\Support\Facades\DB::table('tbl_cms_menu')->insert([
                'id'         => 7,
                'menu_name'  => 'Document Approval',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Grant full permissions to Admin (user id 1) for menu 7
        $permExists = \Illuminate\Support\Facades\DB::table('tbl_user_access_permission')
            ->where('id_user', 1)
            ->where('id_cms_menu', 7)
            ->exists();

        if (!$permExists) {
            \Illuminate\Support\Facades\DB::table('tbl_user_access_permission')->insert([
                'id_user'     => 1,
                'id_cms_menu' => 7,
                'create'      => 1,
                'read'        => 1,
                'update'      => 1,
                'delete'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_document_information', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'decline_reason']);
        });

        \Illuminate\Support\Facades\DB::table('tbl_user_access_permission')->where('id_cms_menu', 7)->delete();
        \Illuminate\Support\Facades\DB::table('tbl_cms_menu')->where('id', 7)->delete();
    }
};
