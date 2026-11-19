<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GAD Accomplishment Report FY {{ $report->year }} - {{ $report->department->department_name ?? 'Official Report' }}</title>
    <link href="{{ asset('assets/img/cdo-seal.png') }}" rel="icon" />
    <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet" />
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm 15mm 10mm;
        }

        * {
            box-sizing: border-box;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px 0;
            color: #111827;
            font-size: 11px;
            line-height: 1.35;
        }

        /* Screen Toolbar */
        .no-print-toolbar {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-print {
            background: #be185d;
            color: #ffffff;
        }
        .btn-print:hover { background: #9d174d; color: #fff; }

        .btn-back {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
        }
        .btn-back:hover { background: #e2e8f0; }

        /* Report Paper Sheet */
        .report-page {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px 32px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }

        /* Official Header */
        .report-header {
            text-align: center;
            margin-bottom: 18px;
            position: relative;
        }

        .report-header img.seal {
            position: absolute;
            left: 20px;
            top: 0;
            width: 65px;
            height: 65px;
        }

        .report-header img.gad-logo {
            position: absolute;
            right: 20px;
            top: 0;
            width: 70px;
        }

        .report-header .republic { font-size: 11px; font-weight: 600; text-transform: uppercase; color: #475569; }
        .report-header .lgu { font-size: 14px; font-weight: 800; text-transform: uppercase; color: #0f172a; margin: 2px 0; }
        .report-header .office { font-size: 12.5px; font-weight: 700; color: #831843; }
        .report-header .title {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 8px;
            color: #831843;
            border-bottom: 2px solid #831843;
            display: inline-block;
            padding-bottom: 2px;
        }

        /* Summary Meta Bar */
        .meta-summary {
            display: flex;
            justify-content: space-between;
            background: #fdf2f8;
            border: 1px solid #fbcfe8;
            padding: 8px 14px;
            border-radius: 4px;
            margin-bottom: 14px;
            font-size: 11px;
        }
        .meta-summary span strong { color: #831843; }

        /* Section Headings */
        .section-title {
            background: #831843;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 10px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-top: 14px;
            margin-bottom: 0;
            border-radius: 3px 3px 0 0;
        }

        /* Printable Tables */
        table.printable-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10px;
        }

        table.printable-table th,
        table.printable-table td {
            border: 1px solid #94a3b8;
            padding: 6px 7px;
            vertical-align: top;
            line-height: 1.35;
        }

        table.printable-table th {
            background: #fce7f3;
            color: #831843;
            font-weight: 700;
            text-align: center;
            vertical-align: middle;
        }

        table.printable-table td.amount,
        table.printable-table th.amount {
            text-align: right;
            white-space: nowrap;
        }

        table.printable-table tfoot td {
            background: #fdf2f8;
            font-weight: 800;
            border-top: 2px solid #831843;
        }

        /* Signature Blocks */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 28%;
            text-align: center;
            font-size: 10.5px;
        }

        .signature-box .role-label {
            font-weight: 600;
            margin-bottom: 45px;
            color: #475569;
        }

        .signature-box .signee-name {
            font-weight: 800;
            text-decoration: underline;
            text-transform: uppercase;
            color: #0f172a;
        }

        .signature-box .signee-title {
            color: #64748b;
            font-size: 10px;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .report-page {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }

            table.printable-table th {
                background-color: #fce7f3 !important;
                color: #831843 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .section-title {
                background-color: #831843 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table.printable-table tfoot td {
                background-color: #fdf2f8 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- On-screen toolbar -->
    <div class="no-print-toolbar">
        <div>
            <a href="{{ route('accomplishment.show', $report->id) }}" class="btn-action btn-back">
                <i class="bi bi-arrow-left"></i> Back to Accomplishment Report
            </a>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="font-size: 12px; color: #64748b;">A4 Landscape Orientation</span>
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="bi bi-printer-fill"></i> Print / Save as PDF
            </button>
        </div>
    </div>

    <!-- Official Report Sheet -->
    <div class="report-page">
        
        <!-- Header -->
        <div class="report-header">
            <img src="{{ asset('assets/img/cdo-seal.png') }}" alt="LGU Seal" class="seal">
            <img src="{{ asset('assets/img/gad-logo.png') }}" alt="GAD Logo" class="gad-logo">
            
            <div class="republic">Republic of the Philippines</div>
            <div class="lgu">City Government of Cagayan de Oro</div>
            <div class="office">{{ $report->department->department_name ?? 'GAD Focal Point System' }}</div>
            <div class="title">ANNUAL GENDER AND DEVELOPMENT (GAD) ACCOMPLISHMENT REPORT</div>
            <div style="font-weight: 700; font-size: 12px; margin-top: 2px;">Fiscal Year {{ $report->year }}</div>
        </div>

        <!-- Meta Summary -->
        <div class="meta-summary">
            <span><strong>Office / Department:</strong> {{ $report->department->department_name ?? 'N/A' }}</span>
            <span><strong>Total Office Budget:</strong> Php {{ number_format($report->total_office_budget, 2) }}</span>
            <span><strong>Total GAD Expenditure:</strong> Php {{ number_format($report->total_gad_expenditure, 2) }} ({{ number_format($report->gad_percentage, 2) }}%)</span>
            <span><strong>Status:</strong> {{ $report->status }}</span>
        </div>

        <!-- Section 1: Org & Client Focused Accomplishments -->
        <div class="section-title">A. Organization-Focused and Client-Focused Accomplishments</div>
        <table class="printable-table">
            <thead>
                <tr>
                    <th style="width: 14%;">Gender Issue / GAD Mandate</th>
                    <th style="width: 12%;">GAD Objective</th>
                    <th style="width: 12%;">Relevant LGU Program / Project</th>
                    <th style="width: 13%;">GAD Activity</th>
                    <th style="width: 13%;">Performance Target & Indicator</th>
                    <th style="width: 12%;">Actual Results</th>
                    <th style="width: 8%;">Approved GAD Budget</th>
                    <th style="width: 8%;">Actual Cost / Expenditure</th>
                    <th style="width: 8%;">Variance / Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumApproved = 0;
                    $sumActual = 0;
                @endphp
                @forelse ($report->entries as $entry)
                @php
                    $sumApproved += (float)$entry->approved_gad_budget;
                    $sumActual += (float)$entry->actual_cost;
                @endphp
                <tr>
                    <td>{{ $entry->gender_issue }}</td>
                    <td>{{ $entry->gad_objective }}</td>
                    <td>{{ $entry->lgu_program }}</td>
                    <td>{{ $entry->gad_activity }}</td>
                    <td>{{ $entry->performance_indicator }}</td>
                    <td>{{ $entry->actual_results }}</td>
                    <td class="amount">{{ number_format($entry->approved_gad_budget, 2) }}</td>
                    <td class="amount" style="font-weight: 700;">{{ number_format($entry->actual_cost, 2) }}</td>
                    <td>{{ $entry->variance_remarks }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #64748b; padding: 12px;">No organization/client-focused accomplishments encoded.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; text-transform: uppercase;">Sub-Total (Org & Client Focused):</td>
                    <td class="amount">Php {{ number_format($sumApproved, 2) }}</td>
                    <td class="amount">Php {{ number_format($sumActual, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Section 2: Attributed Programs Accomplishments -->
        <div class="section-title" style="margin-top: 10px;">B. Attributed Programs (HGDG Assessment)</div>
        <table class="printable-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 32%;">Title of LGU Program / Project</th>
                    <th style="width: 20%;">HGDG PIMME/FIMME Score</th>
                    <th style="width: 14%;">Total Annual Program Cost</th>
                    <th style="width: 14%;">GAD Attributed Program Cost</th>
                    <th style="width: 14%;">Variance / Remarks</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumTotalCost = 0;
                    $sumAttributedCost = 0;
                @endphp
                @forelse ($report->attributedPrograms as $index => $attr)
                @php
                    $sumTotalCost += (float)$attr->total_annual_cost;
                    $sumAttributedCost += (float)$attr->gad_attributed_cost;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $attr->title }}</td>
                    <td>{{ $attr->checklist_score_desc }}</td>
                    <td class="amount">Php {{ number_format($attr->total_annual_cost, 2) }}</td>
                    <td class="amount" style="font-weight: 700;">Php {{ number_format($attr->gad_attributed_cost, 2) }}</td>
                    <td>{{ $attr->variance_remarks }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 12px;">No attributed programs encoded.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; text-transform: uppercase;">Sub-Total (Attributed Programs):</td>
                    <td class="amount">Php {{ number_format($sumTotalCost, 2) }}</td>
                    <td class="amount">Php {{ number_format($sumAttributedCost, 2) }}</td>
                    <td></td>
                </tr>
                <tr style="background: #fce7f3; font-size: 11px;">
                    <td colspan="3" style="text-align: right; text-transform: uppercase; font-weight: 800; color: #831843;">GRAND TOTAL GAD EXPENDITURE (A + B):</td>
                    <td colspan="2" class="amount" style="font-weight: 800; color: #831843;">Php {{ number_format($report->total_gad_expenditure, 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="role-label">Prepared by:</div>
                <div class="signee-name">{{ $report->creator->name ?? 'GAD Focal Point / Encoder' }}</div>
                <div class="signee-title">GAD Alternate / Focal Person</div>
            </div>

            <div class="signature-box">
                <div class="role-label">Reviewed by:</div>
                <div class="signee-name">City Planning and Development Office</div>
                <div class="signee-title">Technical Working Group</div>
            </div>

            <div class="signature-box">
                <div class="role-label">Approved by:</div>
                <div class="signee-name">{{ $report->department->department_name ?? 'Head of Office / Department' }}</div>
                <div class="signee-title">Department Head / Local Chief Executive</div>
            </div>
        </div>

    </div>

</body>
</html>
