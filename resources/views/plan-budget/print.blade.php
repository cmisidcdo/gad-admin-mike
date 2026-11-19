<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GAD Plan and Budget FY {{ $plan->year }} - {{ $plan->department->department_name ?? 'Official Report' }}</title>
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
            background: #2563eb;
            color: #ffffff;
        }
        .btn-print:hover { background: #1d4ed8; color: #fff; }

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
        .report-header .office { font-size: 12.5px; font-weight: 700; color: #1e3a8a; }
        .report-header .title {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-top: 8px;
            color: #0f2d59;
            border-bottom: 2px solid #0f2d59;
            display: inline-block;
            padding-bottom: 2px;
        }

        /* Summary Meta Bar */
        .meta-summary {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 14px;
            border-radius: 4px;
            margin-bottom: 14px;
            font-size: 11px;
        }
        .meta-summary span strong { color: #0f2d59; }

        /* Section Headings */
        .section-title {
            background: #1e3a5f;
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
            background: #e2e8f0;
            color: #0f172a;
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
            background: #f1f5f9;
            font-weight: 800;
            border-top: 2px solid #0f2d59;
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
                background-color: #e2e8f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .section-title {
                background-color: #1e3a5f !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            table.printable-table tfoot td {
                background-color: #f1f5f9 !important;
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
            <a href="{{ route('plan-budget.show', $plan->id) }}" class="btn-action btn-back">
                <i class="bi bi-arrow-left"></i> Back to Plan & Budget
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
            <div class="office">{{ $plan->department->department_name ?? 'GAD Focal Point System' }}</div>
            <div class="title">ANNUAL GENDER AND DEVELOPMENT (GAD) PLAN AND BUDGET</div>
            <div style="font-weight: 700; font-size: 12px; margin-top: 2px;">Fiscal Year {{ $plan->year }}</div>
        </div>

        <!-- Meta Summary -->
        <div class="meta-summary">
            <span><strong>Office / Department:</strong> {{ $plan->department->department_name ?? 'N/A' }}</span>
            <span><strong>Total Office Budget:</strong> Php {{ number_format($plan->total_office_budget, 2) }}</span>
            <span><strong>Total GAD Budget:</strong> Php {{ number_format($plan->total_gad_budget, 2) }} ({{ number_format($plan->gad_percentage, 2) }}%)</span>
            <span><strong>Status:</strong> {{ $plan->status }}</span>
        </div>

        <!-- Section 1: Org & Client Focused Form -->
        <div class="section-title">A. Organization-Focused and Client-Focused Activities</div>
        <table class="printable-table">
            <thead>
                <tr>
                    <th style="width: 7%;">Category</th>
                    <th style="width: 14%;">Gender Issue / GAD Mandate</th>
                    <th style="width: 12%;">GAD Objective</th>
                    <th style="width: 13%;">Relevant LGU Program / Project</th>
                    <th style="width: 14%;">GAD Activity</th>
                    <th style="width: 14%;">Performance Target & Indicator</th>
                    <th style="width: 8%;">MOOE</th>
                    <th style="width: 8%;">PS</th>
                    <th style="width: 8%;">CO</th>
                    <th style="width: 8%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumMoe = 0;
                    $sumPs = 0;
                    $sumCo = 0;
                @endphp
                @forelse ($plan->entries as $entry)
                @php
                    $rowTotal = (float)$entry->moe + (float)$entry->ps + (float)$entry->co;
                    $sumMoe += (float)$entry->moe;
                    $sumPs += (float)$entry->ps;
                    $sumCo += (float)$entry->co;
                @endphp
                <tr>
                    <td>{{ $entry->focused }}</td>
                    <td>{{ $entry->gender_issue }}</td>
                    <td>{{ $entry->gad_objective }}</td>
                    <td>{{ $entry->lgu_program }}</td>
                    <td>{{ $entry->gad_activity }}</td>
                    <td>{{ $entry->performance_indicator }}</td>
                    <td class="amount">{{ number_format($entry->moe, 2) }}</td>
                    <td class="amount">{{ number_format($entry->ps, 2) }}</td>
                    <td class="amount">{{ number_format($entry->co, 2) }}</td>
                    <td class="amount" style="font-weight: 700;">{{ number_format($rowTotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #64748b; padding: 12px;">No organization/client-focused entries encoded.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; text-transform: uppercase;">Sub-Total (Org & Client Focused):</td>
                    <td class="amount">Php {{ number_format($sumMoe, 2) }}</td>
                    <td class="amount">Php {{ number_format($sumPs, 2) }}</td>
                    <td class="amount">Php {{ number_format($sumCo, 2) }}</td>
                    <td class="amount">Php {{ number_format($sumMoe + $sumPs + $sumCo, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Section 2: Attributed Programs -->
        <div class="section-title" style="margin-top: 10px;">B. Attributed Programs (HGDG Assessment)</div>
        <table class="printable-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No.</th>
                    <th style="width: 34%;">Title of LGU Program / Project</th>
                    <th style="width: 20%;">Checklist Used</th>
                    <th style="width: 12%;">HGDG Score</th>
                    <th style="width: 14%;">Total Annual Program Budget</th>
                    <th style="width: 14%;">GAD Attributed Budget</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumAnnualBudget = 0;
                    $sumAttributedBudget = 0;
                @endphp
                @forelse ($plan->attributedPrograms as $index => $attr)
                @php
                    $sumAnnualBudget += (float)$attr->total_annual_budget;
                    $sumAttributedBudget += (float)$attr->gad_attributed_budget;
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $attr->title }}</td>
                    <td>{{ $attr->checklist }}</td>
                    <td style="text-align: center;">{{ rtrim(rtrim(number_format($attr->hgdg_score, 2), '0'), '.') }}</td>
                    <td class="amount">Php {{ number_format($attr->total_annual_budget, 2) }}</td>
                    <td class="amount" style="font-weight: 700;">Php {{ number_format($attr->gad_attributed_budget, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 12px;">No attributed programs encoded.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; text-transform: uppercase;">Sub-Total (Attributed Programs):</td>
                    <td class="amount">Php {{ number_format($sumAnnualBudget, 2) }}</td>
                    <td class="amount">Php {{ number_format($sumAttributedBudget, 2) }}</td>
                </tr>
                <tr style="background: #e2e8f0; font-size: 11px;">
                    <td colspan="4" style="text-align: right; text-transform: uppercase; font-weight: 800;">GRAND TOTAL GAD BUDGET (A + B):</td>
                    <td colspan="2" class="amount" style="font-weight: 800; color: #1e3a8a;">Php {{ number_format($plan->total_gad_budget, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="role-label">Prepared by:</div>
                <div class="signee-name">{{ $plan->creator->name ?? 'GAD Focal Point / Encoder' }}</div>
                <div class="signee-title">GAD Alternate / Focal Person</div>
            </div>

            <div class="signature-box">
                <div class="role-label">Reviewed by:</div>
                <div class="signee-name">City Planning and Development Office</div>
                <div class="signee-title">Technical Working Group</div>
            </div>

            <div class="signature-box">
                <div class="role-label">Approved by:</div>
                <div class="signee-name">{{ $plan->department->department_name ?? 'Head of Office / Department' }}</div>
                <div class="signee-title">Department Head / Local Chief Executive</div>
            </div>
        </div>

    </div>

</body>
</html>
