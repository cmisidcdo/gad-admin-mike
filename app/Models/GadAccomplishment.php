<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GadAccomplishment extends Model
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
        'ar_attachment', 'reviewer_attachment', 'attached_to_gpb_id', 'created_by',
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
        return $this->hasMany(GadAccomplishmentEntry::class);
    }

    public function attributedPrograms()
    {
        return $this->hasMany(GadAccomplishmentAttributedProgram::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(GadAccomplishmentStatusLog::class)->latest();
    }

    public function gpbPlan()
    {
        return $this->belongsTo(GadPlan::class, 'attached_to_gpb_id');
    }

    /**
     * Total GAD Expenditure = sum of actual_cost from entries + sum of gad_attributed_cost
     */
    public function getTotalGadExpenditureAttribute(): float
    {
        $entries = (float) $this->entries()->sum('actual_cost');
        $attributed = (float) $this->attributedPrograms()->sum('gad_attributed_cost');

        return $entries + $attributed;
    }

    public function getGadPercentageAttribute(): float
    {
        return $this->total_office_budget > 0
            ? ($this->total_gad_expenditure / (float) $this->total_office_budget) * 100
            : 0;
    }
}

