<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblDocumentData extends Model
{
    use HasFactory;

    public $table = 'tbl_document_data';
    protected $primaryKey = 'trans_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'trans_id',
        'document_id',
        'document_desc',
        'file_name',
        'file_type',
        'document_data', // where the file is saved.
        'file_size',
        'is_active',
        'upload_user',
    ];

    protected $casts = [
        'trans_id' => 'string',
    ];

    public function scopeSearch($query, $value)
    {
        // $query->where('document_desc', 'like', "%{$value}%")->orWhere('file_name', 'like', "%{$value}%")->orWhere('upload_user', 'like', "%{$value}%");
        return $query->where('document_desc', 'like', '%' . $value . '%')
            ->orWhere('file_name', 'like', '%' . $value . '%')
            ->orWhere('upload_user', 'like', '%' . $value . '%');
    }
}
