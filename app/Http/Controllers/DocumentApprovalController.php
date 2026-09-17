<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblCmsMenu;
use App\Models\TblUserAccessPermission;

class DocumentApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = TblUserAccessPermission::where('id_user', auth()->user()->id)
            ->where('id_cms_menu', '7')
            ->first();

        if ($permissions && ($permissions->create == '1' || $permissions->read == '1' || $permissions->update == '1' || $permissions->delete == '1')) {
            return view('document-approval');
        }

        return abort(404);
    }
}
