<?php

namespace App\Services;

use App\Models\GadPlan;
use App\Models\GadAccomplishment;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GadExcelExportService
{
    /**
     * Export Plan and Budget to clean Native Excel XML (.xls)
     */
    public static function exportPlan(GadPlan $plan): StreamedResponse
    {
        $plan->load(['entries', 'attributedPrograms', 'department', 'creator']);
        $deptName = $plan->department->department_name ?? 'GAD Office';
        $filename = 'GAD_Plan_and_Budget_FY' . $plan->year . '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $deptName) . '.xls';

        return response()->streamDownload(function () use ($plan) {
            echo view('exports.plan-budget-excel', [
                'plan' => $plan,
                'entries' => $plan->entries,
                'attributed' => $plan->attributedPrograms,
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export Accomplishment Report to clean Native Excel XML (.xls)
     */
    public static function exportAccomplishment(GadAccomplishment $report): StreamedResponse
    {
        $report->load(['entries', 'attributedPrograms', 'department', 'creator']);
        $deptName = $report->department->department_name ?? 'GAD Office';
        $filename = 'GAD_Accomplishment_Report_FY' . $report->year . '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $deptName) . '.xls';

        return response()->streamDownload(function () use ($report) {
            echo view('exports.accomplishment-excel', [
                'report' => $report,
                'entries' => $report->entries,
                'attributed' => $report->attributedPrograms,
            ])->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
