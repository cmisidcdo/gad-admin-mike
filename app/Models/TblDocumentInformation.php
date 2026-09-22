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

    /**
     * Get document submission year.
     */
    public function getYearAttribute()
    {
        if ($this->created_at) {
            return $this->created_at->format('Y');
        }
        if (!empty($this->create_date)) {
            $parts = explode(' ', $this->create_date);
            $dateParts = explode('/', $parts[0]);
            if (isset($dateParts[2]) && is_numeric($dateParts[2])) {
                return $dateParts[2];
            }
        }
        return null;
    }

    /**
     * Get document submission semester (1 or 2).
     */
    public function getSemesterAttribute()
    {
        $month = null;
        if ($this->created_at) {
            $month = (int) $this->created_at->format('n');
        } elseif (!empty($this->create_date)) {
            $parts = explode(' ', $this->create_date);
            $dateParts = explode('/', $parts[0]);
            if (isset($dateParts[0]) && is_numeric($dateParts[0])) {
                $month = (int) $dateParts[0];
            }
        }
        return $month ? ($month <= 6 ? 1 : 2) : null;
    }

    /**
     * Get human-friendly semester label.
     */
    public function getSemesterLabelAttribute()
    {
        $sem = $this->semester;
        return $sem ? ($sem == 1 ? '1st Sem' : '2nd Sem') : '—';
    }


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
