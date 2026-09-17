<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblUserSectorPermission extends Model
{
    use HasFactory;

    public $table = 'tbl_user_sector_permission';

    protected $fillable = [
        'id_user',
        'sector_id',
        'sub_sector_id',
    ];

    /**
     * Relationship to the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relationship to the sector.
     */
    public function sector()
    {
        return $this->belongsTo(TblRefSector::class, 'sector_id', 'sector_id');
    }

    /**
     * Relationship to the sub-sector.
     */
    public function subSector()
    {
        return $this->belongsTo(TblRefSubSector::class, 'sub_sector_id', 'sub_sector_id');
    }
}
