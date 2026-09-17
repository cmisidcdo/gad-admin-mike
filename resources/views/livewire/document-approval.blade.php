<div>
    <div class="pagetitle d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="card-title p-0 mb-1">Document Approval & Review</h5>
            <nav>
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Document Approval</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- FLASH STATUS MESSAGE -->
    @if(session('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i>
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- STATUS TABS -->
    <div class="card mb-3 shadow-sm border-0">
        <div class="card-body p-2">
            <ul class="nav nav-pills nav-fill gap-2">
                <li class="nav-item">
                    <button
                        wire:click="setStatusFilter('pending')"
                        class="nav-link {{ $statusFilter === 'pending' ? 'active bg-warning text-dark fw-bold' : 'text-secondary' }} d-flex align-items-center justify-content-center gap-2 py-2"
                    >
                        <i class="bi bi-clock-history"></i>
                        <span>Pending Approval</span>
                        <span class="badge {{ $statusFilter === 'pending' ? 'bg-dark text-white' : 'bg-warning text-dark' }} rounded-pill">
                            {{ $pendingCount }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        wire:click="setStatusFilter('approved')"
                        class="nav-link {{ $statusFilter === 'approved' ? 'active bg-success text-white fw-bold' : 'text-secondary' }} d-flex align-items-center justify-content-center gap-2 py-2"
                    >
                        <i class="bi bi-check2-circle"></i>
                        <span>Approved</span>
                        <span class="badge {{ $statusFilter === 'approved' ? 'bg-white text-success' : 'bg-success text-white' }} rounded-pill">
                            {{ $approvedCount }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        wire:click="setStatusFilter('declined')"
                        class="nav-link {{ $statusFilter === 'declined' ? 'active bg-danger text-white fw-bold' : 'text-secondary' }} d-flex align-items-center justify-content-center gap-2 py-2"
                    >
                        <i class="bi bi-x-circle"></i>
                        <span>Declined</span>
                        <span class="badge {{ $statusFilter === 'declined' ? 'bg-white text-danger' : 'bg-danger text-white' }} rounded-pill">
                            {{ $declinedCount }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        wire:click="setStatusFilter('all')"
                        class="nav-link {{ $statusFilter === 'all' ? 'active bg-primary text-white fw-bold' : 'text-secondary' }} d-flex align-items-center justify-content-center gap-2 py-2"
                    >
                        <i class="bi bi-collection"></i>
                        <span>All Documents</span>
                        <span class="badge {{ $statusFilter === 'all' ? 'bg-white text-primary' : 'bg-secondary text-white' }} rounded-pill">
                            {{ $totalCount }}
                        </span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- SEARCH & SECTOR FILTERS -->
    <div class="row g-2 mb-3 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text fw-medium" style="background-color: #014C8F26;">
                    <i class="bi bi-search me-1"></i> Search
                </span>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    class="form-control"
                    placeholder="Search by description, source, document type, sector..."
                >
            </div>
        </div>
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Sector</span>
                <select wire:model.live="sectorFilter" class="form-select" @if($sectors->isEmpty()) disabled @endif>
                    <option value="">{{ $sectors->isEmpty() ? 'No Sector Assigned' : 'All Allowed Sectors' }}</option>
                    @foreach($sectors as $s)
                    <option value="{{ $s->sector_id }}">{{ $s->sector_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Sub Sector</span>
                <select wire:model.live="subSectorFilter" class="form-select" @if(empty($sectorFilter)) disabled @endif>
                    <option value="">{{ empty($sectorFilter) ? 'Select Sector first...' : 'All Allowed Sub-Sectors' }}</option>
                    @foreach($subSectors as $sub)
                    <option value="{{ $sub->sub_sector_id }}">{{ $sub->sub_sector_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    @if($noRecords)
    <div class="card shadow-sm border-0 py-5 text-center">
        <div class="card-body">
            @if($hasNoSectorPermissions)
            <i class="bi bi-shield-lock text-warning" style="font-size: 3rem;"></i>
            <h5 class="mt-3 fw-bold text-dark">No Sector or Sub-Sector Assigned</h5>
            <p class="text-muted mb-0">Your account does not have permission for any sector. You cannot review or approve documents.</p>
            @else
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <h6 class="mt-2 text-muted">No documents found matching the criteria.</h6>
            @endif
        </div>
    </div>
    @else
    <div class="pagination-info pb-2 small text-muted">
        Showing Page {{ $currentPage }} of {{ $totalPages }} &mdash; Total Records: {{ $totalRecords }}
    </div>

    <div class="table-responsive shadow-sm rounded" wire:loading.class="opacity-50">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="custom-thead-document-archive text-center">
                <tr>
                    <th scope="col" style="width: 140px;">Actions</th>
                    <th scope="col" style="width: 110px;">Status</th>
                    <th scope="col">Document Description</th>
                    <th scope="col">Sector & Sub-Sector</th>
                    <th scope="col">Document Type</th>
                    <th scope="col">Source</th>
                    <th scope="col">Submitted By</th>
                    <th scope="col">Review Info</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr wire:key="{{ $item->trans_id }}">
                    <!-- Actions -->
                    <td class="text-center py-2">
                        <div class="btn-group btn-group-sm" role="group">
                            <!-- Review / View Modal -->
                            <button
                                wire:click="reviewDocument('{{ $item->trans_id }}')"
                                class="btn btn-outline-primary"
                                title="Review Document & Files"
                            >
                                <i class="bi bi-eye"></i> Review
                            </button>

                            @if($item->approval_status === 'pending')
                            <!-- Quick Approve -->
                            <button
                                wire:click="approve('{{ $item->trans_id }}')"
                                wire:confirm="Are you sure you want to APPROVE this document?"
                                class="btn btn-success"
                                title="Quick Approve"
                            >
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <!-- Quick Decline -->
                            <button
                                wire:click="openDeclineModal('{{ $item->trans_id }}')"
                                class="btn btn-danger"
                                title="Decline Document"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="text-center">
                        @if($item->approval_status === 'approved')
                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                            <i class="bi bi-check-circle me-1"></i> Approved
                        </span>
                        @elseif($item->approval_status === 'declined')
                        <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                            <i class="bi bi-x-circle me-1"></i> Declined
                        </span>
                        @else
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">
                            <i class="bi bi-clock me-1"></i> Pending
                        </span>
                        @endif
                    </td>

                    <!-- Description -->
                    <td class="fw-semibold">
                        {{ $item->document_desc }}
                    </td>

                    <!-- Sector & Sub-Sector -->
                    <td>
                        <div class="fw-medium small text-primary">{{ $item->sector_name }}</div>
                        <div class="text-muted small">{{ $item->sub_sector_name }}</div>
                    </td>

                    <!-- Document Type -->
                    <td class="small">
                        {{ $item->doctype_desc }}
                    </td>

                    <!-- Source -->
                    <td class="small text-muted">
                        {{ $item->document_source }}
                    </td>

                    <!-- Submitted By & Date -->
                    <td class="small">
                        <div class="fw-medium"><i class="bi bi-person me-1"></i>{{ $item->create_user ?? 'N/A' }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            {{ $item->created_at ? $item->created_at->format('M d, Y h:i A') : 'N/A' }}
                        </div>
                    </td>

                    <!-- Review Info -->
                    <td class="small">
                        @if($item->approved_by)
                        <div><strong class="text-muted">By:</strong> {{ $item->approved_by }}</div>
                        @if($item->approved_at)
                        <div class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($item->approved_at)->format('M d, Y h:i A') }}</div>
                        @endif
                        @if($item->decline_reason)
                        <div class="text-danger mt-1" style="font-size: 0.8rem;" title="{{ $item->decline_reason }}">
                            <strong>Reason:</strong> {{ Str::limit($item->decline_reason, 40) }}
                        </div>
                        @endif
                        @else
                        <span class="text-muted fst-italic">Not reviewed yet</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $data->links('vendor.livewire.custom-pagination-links') }}
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════ -->
    <!-- ═══════════════════════════════════════════════════ -->
    <!-- REVIEW DOCUMENT MODAL                               -->
    <!-- ═══════════════════════════════════════════════════ -->
    @if($isReviewModalOpen && !empty($selectedDocData))
    <div class="modal fade show" id="reviewModal" tabindex="-1" style="display: block; background-color: rgba(1, 76, 143, 0.3);" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #f8f9fa;">
                    <div class="d-flex align-items-center gap-2">
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>Review Document
                        </h5>
                        @if($selectedDocData['approval_status'] === 'approved')
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                        @elseif($selectedDocData['approval_status'] === 'declined')
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Declined</span>
                        @else
                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Pending Review</span>
                        @endif
                    </div>
                    <button type="button" class="btn-close" wire:click="closeReviewModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Left col: Document Details -->
                        <div class="col-md-7 border-end">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-info-circle me-1"></i>Document Information</h6>

                            <table class="table table-sm table-borderless">
                                <tbody>
                                    <tr>
                                        <th style="width: 140px;" class="text-muted">Description:</th>
                                        <td class="fw-bold fs-6">{{ $selectedDocData['document_desc'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Sector:</th>
                                        <td><span class="badge bg-primary-subtle text-primary border">{{ $selectedDocData['sector_name'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Sub-Sector:</th>
                                        <td class="fw-medium">{{ $selectedDocData['sub_sector_name'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Document Type:</th>
                                        <td>{{ $selectedDocData['doctype_desc'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Source:</th>
                                        <td>{{ $selectedDocData['document_source'] }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Submitted By:</th>
                                        <td><i class="bi bi-person me-1"></i>{{ $selectedDocData['create_user'] ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Submitted Date:</th>
                                        <td>{{ $selectedDocData['created_at'] }}</td>
                                    </tr>
                                    @if(!empty($selectedDocData['approved_by']))
                                    <tr class="table-light border-top">
                                        <th class="text-muted">Reviewed By:</th>
                                        <td><strong>{{ $selectedDocData['approved_by'] }}</strong> @if(!empty($selectedDocData['approved_at'])) on {{ $selectedDocData['approved_at'] }} @endif</td>
                                    </tr>
                                    @endif
                                    @if(!empty($selectedDocData['decline_reason']))
                                    <tr class="table-danger">
                                        <th class="text-danger">Decline Reason:</th>
                                        <td class="text-danger fw-semibold">{{ $selectedDocData['decline_reason'] }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Right col: Attached PDF Files -->
                        <div class="col-md-5">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-paperclip me-1"></i>Attached Files ({{ count($attachedFiles) }})</h6>

                            @if(count($attachedFiles) > 0)
                            <div class="list-group gap-2">
                                @foreach($attachedFiles as $file)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded border p-3">
                                    <div class="me-2 text-truncate">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                            <div>
                                                <div class="fw-semibold text-truncate" style="max-width: 220px;" title="{{ $file['file_name'] }}">
                                                    {{ $file['file_name'] }}
                                                </div>
                                                @if(!empty($file['document_desc']))
                                                <small class="text-muted d-block">{{ Str::limit($file['document_desc'], 40) }}</small>
                                                @endif
                                                @if(!empty($file['file_size']))
                                                <small class="text-muted">{{ number_format($file['file_size'] / 1024, 1) }} KB</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group btn-group-sm flex-shrink-0">
                                        <a
                                            href="{{ route('preview-pdf', ['trans_id' => $file['trans_id']]) }}"
                                            target="_blank"
                                            class="btn btn-outline-primary"
                                            title="Preview in new tab"
                                        >
                                            <i class="bi bi-eye"></i> Preview
                                        </a>
                                        <a
                                            href="{{ route('download', ['trans_id' => $file['trans_id']]) }}"
                                            target="_blank"
                                            class="btn btn-outline-secondary"
                                            title="Download PDF"
                                        >
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="alert alert-secondary text-center py-4">
                                <i class="bi bi-file-earmark-x fs-2 d-block mb-2 text-muted"></i>
                                <span class="text-muted small">No attached files for this document.</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" wire:click="closeReviewModal">Close</button>

                    <div class="d-flex gap-2">
                        @if($selectedDocData['approval_status'] !== 'declined')
                        <button
                            type="button"
                            class="btn btn-danger"
                            wire:click="openDeclineModal('{{ $selectedDocData['trans_id'] }}')"
                        >
                            <i class="bi bi-x-circle me-1"></i> Decline Document
                        </button>
                        @endif

                        @if($selectedDocData['approval_status'] !== 'approved')
                        <button
                            type="button"
                            class="btn btn-success"
                            wire:click="approve('{{ $selectedDocData['trans_id'] }}')"
                            wire:confirm="Are you sure you want to APPROVE this document?"
                            wire:loading.attr="disabled"
                            wire:target="approve"
                        >
                            <span wire:loading wire:target="approve" class="spinner-border spinner-border-sm me-1" role="status"></span>
                            <i class="bi bi-check-circle me-1"></i> Approve Document
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- ═══════════════════════════════════════════════════ -->
    <!-- DECLINE REASON MODAL                                -->
    <!-- ═══════════════════════════════════════════════════ -->
    @if($isDeclineModalOpen)
    <div class="modal fade show" id="declineModal" tabindex="-1" style="display: block; background-color: rgba(220, 53, 69, 0.25); z-index: 1060;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Decline Document
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeDeclineModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">
                        Please state the reason why this document is being declined. The reason will be recorded and attached to the document record.
                    </p>
                    <div class="mb-3">
                        <label for="declineReason" class="form-label fw-bold">Decline Reason <span class="text-danger">*</span></label>
                        <textarea
                            id="declineReason"
                            wire:model="declineReason"
                            class="form-control @error('declineReason') is-invalid @enderror"
                            rows="4"
                            placeholder="e.g. Incomplete data, invalid document attachment, wrong category selected..."
                        ></textarea>
                        @error('declineReason')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDeclineModal">Cancel</button>
                    <button
                        type="button"
                        class="btn btn-danger"
                        wire:click="confirmDecline"
                        wire:loading.attr="disabled"
                        wire:target="confirmDecline"
                    >
                        <span wire:loading wire:target="confirmDecline" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        <i class="bi bi-x-circle me-1"></i> Confirm Decline
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
