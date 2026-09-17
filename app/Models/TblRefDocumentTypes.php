<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblRefDocumentTypes extends Model
{
    use HasFactory;

    public $table = 'tbl_ref_document_types';
    protected $primaryKey = 'doctype_code'; //Overwrites default primary key which is `id`. Now, the application will consider `doctype_code` as primary key.
    protected $fillable = [
        'doctype_code',
        'sector',
        'sub_sector',
        'doctype_desc',
        'is_active',
    ];

    // This should ensure that the Livewire component treats the doctype_code as a string, preventing any automatic casting to integers and allowing you to display the complete value in your select field.
    protected $casts = [
        'doctype_code' => 'string',
    ];

    //////////////////////////////////////////////////////OTHER TABLES/////////////////////////////////////////////////////////////
    /**
     * Define a relationship with the other table (replace function name with your actual model name).
     */
    public function tbl_ref_sector()
    {
        return $this->belongsTo(TblRefSector::class, 'sector_id');
    }

    public function tbl_ref_sub_sector()
    {
        return $this->belongsTo(TblRefSubSector::class, 'sub_sector_id');
    }

    public function tbl_ref_document_types()
    {
        return $this->belongsTo(TblRefDocumentTypes::class, 'doctype_code');
    }
    //////////////////////////////////////////////////////OTHER TABLES/////////////////////////////////////////////////////////////



    ////////////////////////////////////////////////////// SEARCH /////////////////////////////////////////////////////////////
    /**
     * Scope Search is helpful for more than one columns.
     * Use a join to fetch the related records.
     */
    public function scopeSearch($query, $value)
    {
        // $query->where('document_desc', 'like', "%{$value}%")->orWhere('document_source', 'like', "%{$value}%")->orWhere('document_type', 'like', "%{$value}%");
        return $query->join('tbl_ref_sector', 'tbl_ref_document_types.sector', '=', 'tbl_ref_sector.sector_id')
            ->join('tbl_ref_sub_sector', 'tbl_ref_document_types.sub_sector', '=', 'tbl_ref_sub_sector.sub_sector_id')
            ->select('tbl_ref_document_types.*', 'tbl_ref_sector.sector_name', 'tbl_ref_sub_sector.sub_sector_name', 'tbl_ref_document_types.doctype_desc') //select('main_table.*', 'anoher_table.column_name', 'another_table2.column_name').
            ->where(function ($query) use ($value) {
                $query->where('sector_name', 'like', "%{$value}%")
                    ->orWhere('sub_sector_name', 'like', "%{$value}%")
                    ->orWhere('doctype_desc', 'like', "%{$value}%")
                    ->orWhere('sector_name', 'like', "%{$value}%");
            });
    }

    ////////////////////////////////////////////////////// END SEARCH /////////////////////////////////////////////////////////////

}
