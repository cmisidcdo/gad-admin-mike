<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GadAccomplishmentAttributedProgram extends Model
{
    protected $guarded = [];

    public function accomplishment()
    {
        return $this->belongsTo(GadAccomplishment::class, 'gad_accomplishment_id');
    }

    public function remarks()
    {
        return $this->morphMany(GadFieldRemark::class, 'remarkable');
    }

    public function remarkFor(string $fieldName): ?string
    {
        return $this->remarks->firstWhere('field_name', $fieldName)?->remarks;
    }
}

