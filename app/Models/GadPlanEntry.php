<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GadPlanEntry extends Model
{
    protected $guarded = [];

    protected $casts = ['date_of_implementation' => 'date'];

    public function plan()
    {
        return $this->belongsTo(GadPlan::class, 'gad_plan_id');
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

