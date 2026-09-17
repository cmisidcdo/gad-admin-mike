<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblRefDepartment extends Model
{
    use HasFactory;

    protected $table = 'tbl_ref_department';
    protected $primaryKey = 'department_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'department_id',
        'department_name',
        'deptcode',
    ];
}
