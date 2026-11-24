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

    /** The created Accomplishment Report (AR) attached to this plan. */
    public function accomplishmentReport()
    {
        return $this->hasOne(GadAccomplishment::class, 'attached_to_gpb_id')->latestOfMany();
    }

    /** Accomplishment Reports of the same office that can be attached to this plan. */
    public function linkableReports()
    {
        return GadAccomplishment::query()
            ->when(
                $this->department_id !== null,
                fn ($q) => $q->where('department_id', $this->department_id),
                fn ($q) => $q->where('created_by', $this->created_by)
            )
            ->orderByDesc('year')
            ->orderByDesc('id');
    }

    /** Attach one AR to this plan (an AR links to a single plan; a plan keeps a single AR). */
    public function attachReport(int $reportId): void
    {
        $report = $this->linkableReports()->findOrFail($reportId);

        GadAccomplishment::where('attached_to_gpb_id', $this->id)
            ->where('id', '!=', $report->id)
            ->update(['attached_to_gpb_id' => null]);

        $report->update(['attached_to_gpb_id' => $this->id]);
    }

    public function detachReport(): void
    {
        GadAccomplishment::where('attached_to_gpb_id', $this->id)->update(['attached_to_gpb_id' => null]);
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

