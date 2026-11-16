<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GadPlanAttributedProgram extends Model
{
    protected $guarded = [];

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

    /**
     * Attributed budget based on HGDG score brackets:
     * <4 = 0%, 4-7.99 = 25%, 8-14.99 = 50%, 15-19.99 = 75%, 20+ = 100%.
     */
    public function getGadAttributedBudgetAttribute(): float
    {
        $score = (float) $this->hgdg_score;
        $rate = match (true) {
            $score >= 20 => 1.0,
            $score >= 15 => 0.75,
            $score >= 8 => 0.50,
            $score >= 4 => 0.25,
            default => 0.0,
        };

        return (float) $this->total_annual_budget * $rate;
    }
}

