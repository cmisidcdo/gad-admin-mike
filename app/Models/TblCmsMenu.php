<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCmsMenu extends Model
{
    use HasFactory;

    public $table = 'tbl_cms_menu';

    protected $fillable = [
        'id_cms_menu',
        'id_user',
        'create',
        'read',
        'update',
        'delete',
    ];
}
