<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblCmsMenu;
use App\Models\TblUserAccessPermission;

class RecycleBinController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menu = TblCmsMenu::all();
        $permissions = TblUserAccessPermission::where('id_user', auth()->user()->id)->where('id_cms_menu', '6')->get();

        foreach ($permissions as $permission) {
            if ($permission->where('id_cms_menu', '6') && ($permission->create == "1" || $permission->read == "1" || $permission->update == "1" || $permission->delete == "1")) {
                return view('recycle-bin');
            } else {
                return abort(404);
            }
        }

        // If no matching permission is found, you might want to handle it accordingly.
        return abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
