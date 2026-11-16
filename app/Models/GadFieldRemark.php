<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GadFieldRemark extends Model
{
    protected $fillable = [
        'remarkable_type',
        'remarkable_id',
        'field_name',
        'remarks',
        'admin_id',
    ];

    public function remarkable()
    {
        return $this->morphTo();
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

