<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GadPlan extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'Encoding process',
        'Submitted to CPDO',
        'Returned to Office',
        'Endorsed to DILG',
    ];

    protected $fillable = [
        'year', 'total_office_budget', 'status', 'status_remarks', 'status_date',
        'ar_attachment', 'reviewer_attachment', 'accomplishment_report', 'created_by',
        'department_id',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department()
    {
        return $this->belongsTo(TblRefDepartment::class, 'department_id', 'department_id');
    }

    protected $casts = [
        'status_date' => 'date',
        'total_office_budget' => 'decimal:2',
    ];

    public function entries()
    {
        return $this->hasMany(GadPlanEntry::class);
    }

    public function attributedPrograms()
    {
        return $this->hasMany(GadPlanAttributedProgram::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(GadPlanStatusLog::class)->latest();
    }

    /** Sum of MOE + PS + CO of org/client-focused entries, plus attributed budgets. */
    public function getTotalGadBudgetAttribute(): float
    {
        $entries = (float) $this->entries()->selectRaw('COALESCE(SUM(moe + ps + co),0) as t')->value('t');
        $attributed = $this->attributedPrograms->sum(fn ($p) => $p->gad_attributed_budget);

        return $entries + $attributed;
    }

    public function getGadPercentageAttribute(): float
    {
        return $this->total_office_budget > 0
            ? ($this->total_gad_budget / (float) $this->total_office_budget) * 100
            : 0;
    }
}

