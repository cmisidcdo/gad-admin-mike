<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblUserAccessPermission extends Model
{
    use HasFactory;

    public $table = 'tbl_user_access_permission';

    protected $fillable = [
        'id_cms_menu',
        'id_user',
        'create',
        'read',
        'update',
        'delete',
    ];
}
