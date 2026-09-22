<?php

namespace App\Services;

use App\Models\TblCmsMenu;
use App\Models\TblUserAccessPermission;
use App\Models\TblUserRole;

class UserPermissionService
{
    /**
     * Assign default module permissions for a newly created user.
     *
     * User role (id = 4):
     *   - Archive: Create = 1, Read = 1, Update = 1, Delete = 0
     *   - All other modules: all 0
     *
     * Administrator role (id = 1):
     *   - All modules: Create = 1, Read = 1, Update = 1, Delete = 1
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public static function assignDefault($user): void
    {
        $role     = TblUserRole::where('user_role_id', $user->user_role_id)->first();
        $roleName = $role ? strtolower(trim($role->user_role_name)) : '';

        $menus = TblCmsMenu::all();

        foreach ($menus as $menu) {
            $menuName  = strtolower(trim($menu->menu_name));
            $isArchive = ($menu->id == 3 || $menuName === 'archive');

            if ($roleName === 'administrator' || $roleName === 'admin' || (int) $user->user_role_id === 1) {
                // Administrator — full access to every module
                TblUserAccessPermission::updateOrCreate(
                    ['id_user' => $user->id, 'id_cms_menu' => $menu->id],
                    ['create' => 1, 'read' => 1, 'update' => 1, 'delete' => 1]
                );
            } elseif ($roleName === 'user' || (int) $user->user_role_id === 4) {
                // User — Archive: CRU only; everything else: no access
                TblUserAccessPermission::updateOrCreate(
                    ['id_user' => $user->id, 'id_cms_menu' => $menu->id],
                    [
                        'create' => $isArchive ? 1 : 0,
                        'read'   => $isArchive ? 1 : 0,
                        'update' => $isArchive ? 1 : 0,
                        'delete' => 0,
                    ]
                );
            } else {
                // Default fallback — treat like User role
                TblUserAccessPermission::updateOrCreate(
                    ['id_user' => $user->id, 'id_cms_menu' => $menu->id],
                    [
                        'create' => $isArchive ? 1 : 0,
                        'read'   => $isArchive ? 1 : 0,
                        'update' => $isArchive ? 1 : 0,
                        'delete' => 0,
                    ]
                );
            }
        }
    }
}
