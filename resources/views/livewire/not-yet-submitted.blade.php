<div>
    <!-- Status and Error Alerts -->
    @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-octagon fs-5 me-2"></i>
            <div>
                {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Top Header & Send for All Button -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
        <div>
            <h5 class="card-title mb-0 pb-0">Users Who Have Not Yet Submitted</h5>
            <p class="text-muted small mb-0">List of active accounts with the <strong>User</strong> role who have not submitted any document within the selected period.</p>
        </div>
        @if($totalNotSubmitted > 0)
        <div class="d-flex align-items-center gap-2">
            @if(!empty($selectedUserIds) && count($selectedUserIds) > 0)
            <button type="button" 
                    wire:click="openSelectedEmailModal" 
                    class="btn btn-warning btn-sm d-flex align-items-center gap-1 shadow-sm text-dark fw-medium">
                <i class="bi bi-envelope-check-fill"></i>
                <span>Send to Selected ({{ count($selectedUserIds) }})</span>
            </button>
            @endif
            <button type="button" 
                    wire:click="openBatchEmailModal" 
                    class="btn btn-primary btn-sm d-flex align-items-center gap-2 shadow-sm fw-medium">
                <i class="bi bi-send-fill"></i>
                <span>Send for All ({{ $totalNotSubmitted }})</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Summary Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border mb-0 shadow-sm" style="border-left: 4px solid #012970 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="background-color: #0129701a; width: 48px; height: 48px;">
                            <i class="bi bi-people fs-4" style="color: #012970;"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Total Active (User Role)</span>
                            <h4 class="mb-0 fw-bold">{{ $totalActiveUsersCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border mb-0 shadow-sm" style="border-left: 4px solid #198754 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="background-color: #1987541a; width: 48px; height: 48px;">
                            <i class="bi bi-check2-circle fs-4 text-success"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Submitted in Period</span>
                            <h4 class="mb-0 fw-bold text-success">{{ $totalSubmittedCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border mb-0 shadow-sm" style="border-left: 4px solid #dc3545 !important;">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="background-color: #dc35451a; width: 48px; height: 48px;">
                                <i class="bi bi-exclamation-octagon fs-4 text-danger"></i>
                            </div>
                            <div>
                                <span class="text-muted small fw-medium">Not Yet Submitted</span>
                                <h4 class="mb-0 fw-bold text-danger">{{ $totalNotSubmitted }}</h4>
                            </div>
                        </div>
                        @if($totalNotSubmitted > 0)
                        <div>
                            <button type="button" 
                                    wire:click="openBatchEmailModal" 
                                    class="btn btn-outline-danger btn-sm py-1 px-2 d-flex align-items-center gap-1 shadow-sm"
                                    title="Send email reminder to all unsubmitted users">
                                <i class="bi bi-envelope-fill"></i>
                                <span class="small fw-semibold">Send for All</span>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section Card -->
    <div class="card border mb-3 bg-light shadow-sm">
        <div class="card-body py-3">
            <!-- Row 1: Year, Semester, Sector, Subsector -->
            <div class="row g-2 align-items-center mb-2">
                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Year</span>
                        <select wire:model.live="selectedYear" class="form-select form-select-sm">
                            <option value="">All Years</option>
                            @foreach($availableYears as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Semester</span>
                        <select wire:model.live="selectedSemester" class="form-select form-select-sm">
                            <option value="1">1st Semester (Jan - Jun)</option>
                            <option value="2">2nd Semester (Jul - Dec)</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Sector</span>
                        <select wire:model.live="selectedSector" class="form-select form-select-sm">
                            <option value="">All Sectors</option>
                            @foreach($sectors as $sec)
                            <option value="{{ $sec->sector_id }}">{{ $sec->sector_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Subsector</span>
                        <select wire:model.live="selectedSubSector" class="form-select form-select-sm" {{ empty($selectedSector) ? 'disabled' : '' }}>
                            <option value="">{{ empty($selectedSector) ? 'Select Sector first...' : 'All Subsectors' }}</option>
                            @foreach($subSectors as $sub)
                            <option value="{{ $sub->sub_sector_id }}">{{ $sub->sub_sector_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Row 2: Office / Department, Search, Reset Filters -->
            <div class="row g-2 align-items-center">
                <div class="col-md-5 col-sm-6">
                    <select wire:model.live="selectedDepartment" class="form-select form-select-sm">
                        <option value="">All Offices / Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5 col-sm-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search user, ID, email...">
                    </div>
                </div>

                <div class="col-md-2 col-sm-12 text-end">
                    @if($search || $selectedDepartment || $selectedYear !== (string) date('Y') || $selectedSemester || $selectedSector || $selectedSubSector)
                    <button type="button" wire:click="resetFilters" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filters
                    </button>
                    @endif
                </div>
            </div>

            @if($errorMessage)
            <div class="alert alert-warning py-2 mt-2 mb-0 small">
                <i class="bi bi-exclamation-triangle me-1"></i>{{ $errorMessage }}
            </div>
            @endif
        </div>
    </div>

    <!-- Results Table -->
    @if($noRecords)
    <div class="card border text-center py-5">
        <div class="card-body">
            @if(empty($search) && empty($selectedDepartment) && empty($selectedSector) && empty($selectedSubSector))
            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-success fw-bold">Great News!</h5>
            <p class="text-muted mb-0">All active users have submitted their documents for the selected period.</p>
            @else
            <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-secondary">No records found</h5>
            <p class="text-muted mb-0">No unsubmitted users match your search or filter criteria.</p>
            @endif
        </div>
    </div>
    @else
    <div>
        <!-- Table Controls & Send for All Action Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between pb-2 gap-2">
            <div class="pagination-info d-flex align-items-center gap-2">
                <span>Showing Page {{ $currentPage }} of {{ $totalPages }} (Total: <strong>{{ $totalRecords }}</strong> users)</span>
                <span class="badge bg-light text-dark border">
                    <i class="bi bi-calendar3 me-1"></i>Period: {{ $currentPeriodLabel }}
                </span>
            </div>

            <!-- Action Buttons Above Table -->
            <div class="d-flex align-items-center gap-2">
                @if(!empty($selectedUserIds) && count($selectedUserIds) > 0)
                <button type="button" 
                        wire:click="openSelectedEmailModal" 
                        class="btn btn-warning btn-sm d-inline-flex align-items-center gap-1 shadow-sm text-dark fw-medium">
                    <i class="bi bi-envelope-check-fill"></i>
                    <span>Send to Selected ({{ count($selectedUserIds) }})</span>
                </button>
                @endif

                <button type="button" 
                        wire:click="openBatchEmailModal" 
                        class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm fw-medium">
                    <i class="bi bi-send-fill"></i>
                    <span>Send for All ({{ $totalRecords }})</span>
                </button>
            </div>
        </div>

        <div class="table-responsive" wire:loading.class="opacity-50" wire:target="search, selectedYear, selectedSemester, selectedSector, selectedSubSector, selectedDepartment, resetFilters">
            <table class="table table-bordered table-hover align-middle">
                <thead class="custom-thead-document-archive">
                    <tr class="text-center">
                        <th scope="col" style="width: 40px;">
                            <input type="checkbox" wire:model.live="selectAll" class="form-check-input" title="Select/Deselect All">
                        </th>
                        <th scope="col" style="width: 50px;">#</th>
                        <th scope="col" style="width: 130px;">User ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Office / Department</th>
                        <th scope="col" style="min-width: 220px;">Not Submitted Sector/Subsector</th>
                        <th scope="col">Email Address</th>
                        <th scope="col" style="width: 120px;">Status</th>
                        <th scope="col" style="width: 110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($notSubmittedUsers as $index => $item)
                    <tr class="text-center {{ !empty($selectedUserIds) && in_array((string)$item->id, $selectedUserIds) ? 'table-primary' : '' }}" wire:key="nys-user-{{ $item->id }}">
                        <td>
                            <input type="checkbox" 
                                   wire:model.live="selectedUserIds" 
                                   value="{{ (string)$item->id }}" 
                                   class="form-check-input">
                        </td>
                        <th scope="row" class="py-3 text-muted">
                            {{ ($currentPage - 1) * 10 + $index + 1 }}
                        </th>
                        <td class="fw-semibold text-start ps-3">{{ $item->user_id }}</td>
                        <td class="text-start ps-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-danger text-white d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 32px; height: 32px; font-size: 13px; font-weight: 600;">
                                    {{ strtoupper(substr($item->name, 0, 1)) }}
                                </div>
                                <span class="fw-medium">{{ $item->name }}</span>
                            </div>
                        </td>
                        <td class="text-start ps-3">
                            {{ $item->department->department_name ?? $item->office ?? 'N/A' }}
                        </td>
                        <td class="text-start ps-3">
                            @if(!empty($item->unsubmitted_sectors) && count($item->unsubmitted_sectors) > 0)
                                <div class="d-flex flex-column gap-1">
                                    @foreach($item->unsubmitted_sectors as $unsub)
                                        @if(!empty($unsub['is_unassigned']))
                                            <span class="badge bg-light text-muted border align-self-start">
                                                <i class="bi bi-dash-circle me-1"></i>No assigned sectors
                                            </span>
                                        @else
                                            <div>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle text-wrap text-start py-1 px-2 d-inline-flex align-items-start" style="font-size: 11.5px; line-height: 1.35; max-width: 320px;">
                                                    <i class="bi bi-x-circle-fill flex-shrink-0 me-1 mt-0.5" style="font-size: 11px;"></i>
                                                    <span>
                                                        <strong>{{ $unsub['sector_name'] }}</strong>
                                                        @if(!empty($unsub['sub_sector_name']))
                                                            &mdash; {{ $unsub['sub_sector_name'] }}
                                                        @endif
                                                    </span>
                                                </span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="badge bg-light text-muted border">
                                    <i class="bi bi-dash-circle me-1"></i>No assigned sectors
                                </span>
                            @endif
                        </td>
                        <td class="text-start ps-3">
                            @if(!empty($item->email))
                            <a href="mailto:{{ $item->email }}" class="text-decoration-none">
                                <i class="bi bi-envelope me-1"></i>{{ $item->email }}
                            </a>
                            @else
                            <span class="badge bg-light text-muted border">No email</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-danger">Not Submitted</span>
                        </td>
                        <td>
                            @if(!empty($item->email))
                            <button type="button" 
                                    wire:click="openSingleEmailModal({{ $item->id }})" 
                                    class="btn btn-sm btn-outline-primary py-1 px-2 d-inline-flex align-items-center gap-1"
                                    title="Send reminder email to {{ $item->name }}">
                                <i class="bi bi-send-fill"></i>
                                <span>Remind</span>
                            </button>
                            @else
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary py-1 px-2 d-inline-flex align-items-center gap-1 opacity-50" 
                                    disabled 
                                    title="User has no email address configured">
                                <i class="bi bi-send"></i>
                                <span>Remind</span>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $notSubmittedUsers->links('vendor.livewire.custom-pagination-links') }}
        </div>
    </div>
    @endif

    <!-- Email Reminder Confirmation & Composition Modal -->
    @if($showEmailModal)
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block; background-color: rgba(0, 0, 0, 0.5); z-index: 1055;" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg">
                <!-- Modal Header -->
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #012970 0%, #014C8F 100%);">
                    <h5 class="modal-title fs-6 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-envelope-paper"></i>
                        @if($emailMode === 'single')
                            Send Reminder Email &mdash; {{ $singleUserName }}
                        @elseif($emailMode === 'selected')
                            Send Reminder to Selected Users ({{ $modalRecipientsCount }})
                        @else
                            Send Reminder for All Unsubmitted Users ({{ $modalRecipientsCount }})
                        @endif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeEmailModal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-4">
                    <!-- Recipient & Period Context Banner -->
                    <div class="p-3 mb-3 rounded border" style="background-color: #f8fafc;">
                        <div class="row g-2 small">
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Reporting Period:</span>
                                <strong class="text-dark">{{ $currentPeriodLabel }}</strong>
                            </div>
                            @if($emailMode === 'single')
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Recipient:</span>
                                <strong class="text-primary">{{ $singleUserName }}</strong>
                                <span class="text-muted d-block">{{ $singleUserDepartment }}</span>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Email Address:</span>
                                @if($singleUserEmail)
                                    <strong class="text-dark">{{ $singleUserEmail }}</strong>
                                @else
                                    <span class="badge bg-danger">No Email On File</span>
                                @endif
                            </div>
                            @else
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Target Users:</span>
                                <strong class="text-dark">{{ $modalRecipientsCount }} user(s) {{ $emailMode === 'selected' ? 'selected' : 'unsubmitted' }}</strong>
                            </div>
                            <div class="col-sm-4">
                                <span class="text-muted d-block">Deliverability:</span>
                                <span class="badge bg-success">{{ $modalValidEmailCount }} with email</span>
                                @if($modalMissingEmailCount > 0)
                                <span class="badge bg-warning text-dark">{{ $modalMissingEmailCount }} skipped (no email)</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($emailMode === 'single' && !empty($singleUserUnsubmittedSectors) && count($singleUserUnsubmittedSectors) > 0)
                    <div class="mb-3 p-2 px-3 rounded border" style="background-color: #fef2f2; border-color: #fecaca !important;">
                        <span class="fw-bold small text-danger d-block mb-1">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Pending Sector / Subsector Requirement(s):
                        </span>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($singleUserUnsubmittedSectors as $unsub)
                                @if(!empty($unsub['is_unassigned']))
                                    <span class="badge bg-light text-muted border">No assigned sectors</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle py-1 px-2">
                                        <i class="bi bi-x-circle me-1"></i>
                                        <strong>{{ $unsub['sector_name'] }}</strong>
                                        @if(!empty($unsub['sub_sector_name']))
                                            &mdash; {{ $unsub['sub_sector_name'] }}
                                        @endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($modalValidEmailCount === 0)
                    <div class="alert alert-danger py-2 small mb-3">
                        <i class="bi bi-exclamation-octagon me-1"></i>
                        @if($emailMode === 'single')
                            This user does not have a valid email address configured and cannot receive notifications.
                        @else
                            None of the target users have a valid email address on file.
                        @endif
                    </div>
                    @endif

                    <!-- Subject Field -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">
                            Email Subject <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               wire:model="emailSubject" 
                               class="form-control form-control-sm @error('emailSubject') is-invalid @enderror" 
                               placeholder="Enter email subject...">
                        @error('emailSubject')
                        <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Custom Message / Instructions -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">
                            Additional Note / Instructions <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea wire:model="emailCustomMessage" 
                                  class="form-control form-control-sm @error('emailCustomMessage') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Add optional custom instructions or deadline notice (e.g. Please submit your files on or before Friday at 5:00 PM)..."></textarea>
                       
            
                    </div>

                    
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" 
                            class="btn btn-secondary btn-sm" 
                            wire:click="closeEmailModal" 
                            wire:loading.attr="disabled"
                            wire:target="sendEmails">
                        Cancel
                    </button>
                    <button type="button" 
                            class="btn btn-primary btn-sm d-flex align-items-center gap-1" 
                            wire:click="sendEmails" 
                            wire:loading.attr="disabled"
                            wire:target="sendEmails"
                            @if($modalValidEmailCount === 0) disabled @endif>
                        <span wire:loading.remove wire:target="sendEmails">
                            <i class="bi bi-send-fill me-1"></i>
                            @if($emailMode === 'single')
                                Send Reminder Email
                            @elseif($emailMode === 'selected')
                                Send to {{ $modalValidEmailCount }} Selected User{{ $modalValidEmailCount > 1 ? 's' : '' }}
                            @else
                                Send for All ({{ $modalValidEmailCount }})
                            @endif
                        </span>
                        <span wire:loading wire:target="sendEmails">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Sending emails, please wait...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
