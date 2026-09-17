<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblDocumentInformation extends Model
{
    use HasFactory;

    protected $table = 'tbl_document_information';
    protected $primaryKey = 'trans_id';
    protected $fillable = [
        'trans_id',
        'sector',
        'sub_sector',
        'document_type',
        'document_desc',
        'document_source',
        'is_active',
        'approval_status',
        'approved_by',
        'approved_at',
        'decline_reason',
        'create_user',
    ];

    protected $casts = [
        'trans_id' => 'string',
        'approved_at' => 'datetime',
    ];


    //////////////////////////////////////////////////////OTHER TABLES/////////////////////////////////////////////////////////////
    /**
     * Define a relationship with the other table (replace function name with your actual model name).
     */
    public function tbl_ref_sector()
    {
        return $this->belongsTo(TblRefSector::class, 'sector', 'sector_id');
    }

    public function tbl_ref_sub_sector()
    {
        return $this->belongsTo(TblRefSubSector::class, 'sub_sector', 'sub_sector_id');
    }

    public function tbl_ref_document_types()
    {
        return $this->belongsTo(TblRefDocumentTypes::class, 'document_type', 'doctype_code');
    }

    public function document_data()
    {
        return $this->hasMany(TblDocumentData::class, 'document_id', 'trans_id');
    }
    //////////////////////////////////////////////////////OTHER TABLES/////////////////////////////////////////////////////////////


    ////////////////////////////////////////////////////// SEARCH /////////////////////////////////////////////////////////////
    /**
     * Scope Search is helpful in searching for more than one columns.
     * Use a join to fetch the related records.
     */
    public function scopeSearch($query, $value)
    {
        // $query->where('document_desc', 'like', "%{$value}%")->orWhere('document_source', 'like', "%{$value}%")->orWhere('document_type', 'like', "%{$value}%");
        return $query->join('tbl_ref_sector', 'tbl_document_information.sector', '=', 'tbl_ref_sector.sector_id')
            ->join('tbl_ref_sub_sector', 'tbl_document_information.sub_sector', '=', 'tbl_ref_sub_sector.sub_sector_id')
            ->join('tbl_ref_document_types', 'tbl_document_information.document_type', '=', 'tbl_ref_document_types.doctype_code')
            ->select('tbl_document_information.*', 'tbl_ref_sector.sector_name', 'tbl_ref_sub_sector.sub_sector_name', 'tbl_ref_document_types.doctype_desc') //select('main_table.*', 'anoher_table.column_name', 'another_table2.column_name').
            ->where(function ($query) use ($value) {
                $query->where('document_desc', 'like', "%{$value}%")
                    ->orWhere('document_source', 'like', "%{$value}%")
                    ->orWhere('doctype_desc', 'like', "%{$value}%")
                    ->orWhere('sector_name', 'like', "%{$value}%")
                    ->orWhere('sub_sector_name', 'like', "%{$value}%");
            });
    }
    ////////////////////////////////////////////////////// END SEARCH /////////////////////////////////////////////////////////////
}
