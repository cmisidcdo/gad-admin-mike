<div>
    <div class="gad-card">
        <div class="gad-title">Office GAD Accomplishment Report</div>

        @if (session()->has('message'))
        <div class="alert alert-success py-1 px-2 mb-2" style="font-size:11px">{{ session('message') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="d-flex gap-3 align-items-center flex-wrap">
                @if ($isAdmin)
                <div>
                    <span class="gad-label">Office / Dept</span>
                    <select class="gad-select" style="max-width: 260px;" wire:model.live="filterDepartment">
                        <option value="">All Offices</option>
                        @foreach ($departments as $d)
                        <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <div class="d-flex align-items-center">
                    <span class="gad-label">My Office</span>
                    <span class="gad-select bg-light fw-bold text-primary">{{ auth()->user()->department->department_name ?? auth()->user()->office ?? 'Unassigned Office' }}</span>
                </div>
                @endif
                <div>
                    <span class="gad-label">Year</span>
                    <select class="gad-select" wire:model.live="filterYear">
                        <option value="">All</option>
                        @foreach ($years as $y)
                        <option value="{{ $y }}">FY {{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <span class="gad-label">Status</span>
                    <select class="gad-select" wire:model.live="filterStatus">
                        <option value="">All</option>
                        @foreach ($statuses as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="button" class="btn-gad-add" wire:click="openAdd"><i class="bi bi-journal-plus me-1"></i>ADD</button>
        </div>

        <div class="gad-table-wrapper">
        <table class="gad-table">
            <thead>
                <tr>
                    @if ($isAdmin)
                    <th style="width:16%">Office / Dept</th>
                    @endif
                    <th style="width:10%">Year</th>
                    <th style="width:25%">Budget</th>
                    <th style="width:{{ $isAdmin ? '25%' : '35%' }}">Status</th>
                    <th style="width:{{ $isAdmin ? '16%' : '22%' }}">Attachments</th>
                    <th style="width:8%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                @php
                $pillClass = $report->status === 'Endorsed to DILG' ? 'endorsed' : '';
                @endphp
                <tr>
                    @if ($isAdmin)
                    <td class="fw-bold" style="color: var(--gad-navy);">
                        <i class="bi bi-building me-1 text-primary"></i>
                        {{ $report->department->department_name ?? $report->department_id ?? 'N/A' }}
                    </td>
                    @endif
                    <td class="text-center fw-bold">FY {{ $report->year }}</td>
                    <td>
                        <div class="fw-bold">Total Office Budget:</div>
                        <div>Php {{ number_format($report->total_office_budget, 2) }}</div>
                        <hr class="my-2">
                        <div class="fw-bold">Total GAD Budget:</div>
                        <div class="budget-red">Php {{ number_format($report->total_gad_expenditure, 2) }} ({{ number_format($report->gad_percentage, 3) }}%)</div>
                    </td>
                    <td>
                        <div class="mb-1"><i class="bi bi-flag-fill me-1"></i><span class="status-pill {{ $pillClass }}">{{ $report->status }}</span></div>
                        <div class="mb-1"><i class="bi bi-clock me-1"></i>{{ optional($report->status_date)->format('F j, Y') }}</div>
                        @if ($report->status_remarks)
                        <div class="status-remarks"><i class="bi bi-chat-left-text me-1"></i>{{ $report->status_remarks }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="mb-2 p-1" style="background: #fbfbfb; border: 1px dashed #ced4da; border-radius: 4px; font-size: 10px;">
                            <a href="#" wire:click.prevent="openAttachLink({{ $report->id }})" class="d-block text-decoration-none fw-bold text-primary mb-1">
                                <i class="bi bi-link-45deg me-1"></i>This AR is attached to
                            </a>
                            @if ($report->gpbPlan)
                            <div class="text-secondary">
                                <div><strong>GPB : FY {{ $report->gpbPlan->year }}</strong></div>
                                <div>Total LGU Budget : Php {{ number_format($report->gpbPlan->total_office_budget, 2) }}</div>
                                <div>Total GAD Budget Php {{ number_format($report->gpbPlan->total_gad_budget, 2) }}</div>
                            </div>
                            @else
                            <div class="text-muted fst-italic">Click to attach to a GPB Plan</div>
                            @endif
                        </div>

                        @if ($report->reviewer_attachment)
                        <a class="attach-chip" target="_blank" href="{{ asset('storage/' . $report->reviewer_attachment) }}"><i class="bi bi-download me-1"></i>Reviewer's Attachment</a>
                        @else
                        <div class="text-danger" style="font-size:10px"><i class="bi bi-exclamation-lg"></i> No Reviewer's Attachment</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('accomplishment.show', $report->id) }}" class="btn-xs view mb-1 text-decoration-none"><i class="bi bi-eye-fill"></i>View</a><br>
                        <a href="{{ route('accomplishment.print', $report->id) }}" target="_blank" class="btn-xs print mb-1 text-decoration-none" style="background:#831843;" title="Print Report"><i class="bi bi-printer-fill"></i>Print</a><br>
                        <a href="{{ route('accomplishment.excel', $report->id) }}" class="btn-xs print mb-1 text-decoration-none" style="background:#16a34a;" title="Export Excel"><i class="bi bi-file-earmark-spreadsheet"></i>Excel</a><br>
                        <button type="button" class="btn-xs track mb-1" wire:click="track({{ $report->id }})"><i class="bi bi-geo-alt-fill"></i>Track</button><br>
                        @if ((int) auth()->user()->user_role_id === 1)
                        <button type="button" class="btn-xs update mb-1" wire:click="openStatus({{ $report->id }})"><i class="bi bi-arrow-repeat"></i>Status</button><br>
                        @endif
                        @if ($report->status === 'Encoding process' || (int) auth()->user()->user_role_id === 1)
                        <button type="button" class="btn-xs delete" wire:click="confirmDelete({{ $report->id }})"><i class="bi bi-trash-fill"></i>Delete</button>
                        @endif
                    </td>
                </tr>
                @endforeach
                @for ($i = $reports->count(); $i < 6; $i++)
                    <tr class="empty-row">
                    @if ($isAdmin)<td></td>@endif
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>
        </div>

        <div class="d-flex flex-column align-items-end mt-4">
            {{ $reports->links() }}
            <div class="mt-2" style="font-size:10px">
                @if ($reports->total() > 0)
                Showing {{ $reports->firstItem() }}-{{ $reports->lastItem() }} of {{ $reports->total() }} items.
                @else
                No items found.
                @endif
            </div>
        </div>
    </div>

    {{-- Add: Primary Information --}}
    @if ($showAdd)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">PRIMARY INFORMATION <button class="x" wire:click="$set('showAdd', false)">&times;</button></div>
            <div class="gad-modal-body">
                @if ($isAdmin)
                <div class="gad-field-row">
                    <span class="gad-label">Office / Dept</span>
                    <select class="gad-select" wire:model="departmentId">
                        <option value="">Select Office</option>
                        @foreach ($departments as $d)
                        <option value="{{ $d->department_id }}">{{ $d->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                @error('departmentId') <div class="error-text mb-1">{{ $message }}</div> @enderror
                @else
                <div class="gad-field-row">
                    <span class="gad-label">Office</span>
                    <span class="gad-select bg-light fw-bold">{{ auth()->user()->department->department_name ?? auth()->user()->office ?? 'My Office' }}</span>
                </div>
                @endif
                <div class="gad-field-row">
                    <span class="gad-label">Year</span>
                    <select class="gad-select" wire:model="year">
                        <option value="">Select Year</option>
                        @foreach ($years as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                @error('year') <div class="error-text mb-1">{{ $message }}</div> @enderror
                <div class="gad-field-row">
                    <span class="gad-label">Total Office Budget</span>
                    <input type="text" inputmode="decimal" autocomplete="off" class="gad-input money-input" wire:model="totalOfficeBudget">
                </div>
                @error('totalOfficeBudget') <div class="error-text mb-2">{{ $message }}</div> @enderror
                <div class="text-end">
                    <button type="button" class="btn-xs save lg" wire:click="save" wire:loading.attr="disabled"><i class="bi bi-floppy"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Link to GPB Plan Modal --}}
    @if ($attachLinkModal)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">ATTACH TO GPB PLAN <button class="x" wire:click="$set('attachLinkModal', false)">&times;</button></div>
            <div class="gad-modal-body">
                <label class="gad-label mb-1">Select GPB Plan</label>
                <select class="gad-select w-100 mb-3" wire:model="selectedGpbId">
                    <option value="">-- None / Unlinked --</option>
                    @foreach ($gpbPlans as $gp)
                    <option value="{{ $gp->id }}">FY {{ $gp->year }} (Budget: Php {{ number_format($gp->total_office_budget, 2) }})</option>
                    @endforeach
                </select>
                <div class="text-end">
                    <button class="btn-xs save lg" wire:click="saveAttachLink"><i class="bi bi-check-lg"></i> Link Plan</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete confirmation --}}
    @if ($deleteId)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">DELETE ACCOMPLISHMENT REPORT <button class="x" wire:click="$set('deleteId', null)">&times;</button></div>
            <div class="gad-modal-body text-center">
                <p>Are you sure you want to delete this accomplishment report?</p>
                <button class="btn-xs lg" style="background:#6c757d" wire:click="$set('deleteId', null)">Cancel</button>
                <button class="btn-xs delete lg" wire:click="delete">Delete</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Track --}}
    @if ($trackReport)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">TRACK - FY {{ $trackReport->year }} <button class="x" wire:click="$set('trackId', null)">&times;</button></div>
            <div class="gad-modal-body">
                @forelse ($trackReport->statusLogs as $log)
                <div class="mb-2">
                    <span class="status-pill">{{ $log->status }}</span>
                    <span class="ms-1">{{ $log->created_at->format('F j, Y g:i A') }}</span>
                    @if ($log->remarks)<div class="status-remarks">{{ $log->remarks }}</div>@endif
                </div>
                @empty
                <p class="mb-0">No status history yet.</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- Admin Change Status --}}
    @if ($statusId)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">CHANGE STATUS <button class="x" wire:click="$set('statusId', null)">&times;</button></div>
            <div class="gad-modal-body">
                <label class="gad-label mb-1">Status</label>
                <select class="gad-select w-100 mb-2" wire:model="newStatus">
                    @foreach ($statuses as $s)<option>{{ $s }}</option>@endforeach
                </select>
                <label class="gad-label mb-1">Remarks</label>
                <textarea class="gad-input w-100 mb-2" rows="3" wire:model="statusRemarks"></textarea>
                @error('statusRemarks') <div class="error-text">{{ $message }}</div> @enderror
                <label class="gad-label mb-1">Reviewer's Attachment</label>
                <input type="file" class="form-control form-control-sm mb-1" wire:model="reviewerFile">
                @error('reviewerFile') <div class="error-text">{{ $message }}</div> @enderror
                <div class="text-end mt-3">
                    <button class="btn-xs save lg" wire:click="saveStatus" wire:loading.attr="disabled" wire:target="saveStatus,reviewerFile"><i class="bi bi-floppy"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

