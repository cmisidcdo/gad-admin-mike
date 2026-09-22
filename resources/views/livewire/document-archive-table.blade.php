<div>
    <!-- CMS MENU ID: 3 - archive page -->
    @php
    $permissions = App\Models\TblUserAccessPermission::where('id_user', auth()->user()->id)->where('id_cms_menu', '3')->first();
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h5 class="card-title mb-0">Document Archive</h5>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Sem</span>
                <select wire:model.live="filterSemester" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="1">1st Semester (Jan - Jun)</option>
                    <option value="2">2nd Semester (Jul - Dec)</option>
                </select>
            </div>
            <div class="input-group input-group-sm" style="width: auto;">
                <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Year</span>
                <select wire:model.live="filterYear" class="form-select form-select-sm">
                    <option value="">All</option>
                    @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- <div class="d-flex align-items-center">
        <div class="input-group mb-3">
            <span class="input-group-text fw-medium" id="basic-addon1" style="background-color: #014C8F26;">Search</span>
            <input wire:model.live.debounce.300ms="search" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1" wire:model="query">
        </div>
    </div> -->
    <!-- ACTION NEEDED: DECLINED DOCUMENTS BANNER -->
    @php
    $myDeclinedCount = App\Models\TblDocumentInformation::where(function ($q) {
        $q->where('create_user', Auth::user()->name)
          ->orWhere('create_user', Auth::user()->user_id);
    })->where('approval_status', 'declined')->count();
    @endphp

    @if($myDeclinedCount > 0)
    <div class="alert alert-danger d-flex align-items-center mb-3 shadow-sm border-danger" role="alert">
        <i class="bi bi-exclamation-octagon-fill fs-3 me-3 text-danger flex-shrink-0"></i>
        <div>
            <div class="fw-bold fs-6">Action Needed: Document Returned for Revision</div>
            <span>You have <strong>{{ $myDeclinedCount }}</strong> document(s) declined by the administrator. Click on the document in the table below to review the feedback and submit your revision.</span>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        <!-- SEARCH & SECTOR FILTERS ROW -->
        <div class="col-12">
            <div class="row g-2 mb-3 align-items-center">
                <!-- Search Input -->
                <div class="{{ $this->isAdministrator() ? 'col-lg-4 col-md-4' : 'col-lg-10 col-md-10' }}">
                    <div class="input-group">
                        <span class="input-group-text fw-medium" id="basic-addon1" style="background-color: #014C8F26;">
                            <i class="bi bi-search me-1"></i> Search
                        </span>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search documents...">
                    </div>
                </div>

                @if($this->isAdministrator())
                <!-- Sector Filter -->
                <div class="col-lg-3 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Sector</span>
                        <select wire:model.live="filterSector" class="form-select" @if($allowedSectors->isEmpty()) disabled @endif>
                            <option value="">{{ $allowedSectors->isEmpty() ? 'No Sector Assigned' : 'All' }}</option>
                            @foreach($allowedSectors as $s)
                            <option value="{{ $s->sector_id }}">{{ $s->sector_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Sub-Sector Filter -->
                <div class="col-lg-3 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Sub Sector</span>
                        <select wire:model.live="filterSubSector" class="form-select" @if(empty($filterSector)) disabled @endif>
                            <option value="">{{ empty($filterSector) ? 'All Sub-Sectors' : 'All Allowed Sub-Sectors' }}</option>
                            @foreach($filterSubSectors as $sub)
                            <option value="{{ $sub->sub_sector_id }}">{{ $sub->sub_sector_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <!-- This will check if the user is allowed to create. Based on the `tbl_user_access_permission` -->
                @if($permissions->create == '1')
                <div class="{{ $this->isAdministrator() ? 'col-lg-2 col-md-2' : 'col-lg-2 col-md-2' }} text-end">
                    <button class="btn btn-primary w-100" wire:click="add" @if($allowedSectors->isEmpty()) disabled title="No sector assigned to your account" @endif>
                        <i class="bi bi-plus-lg me-1"></i> Add
                    </button>
                </div>
                @endif
            </div>
        </div>

    </div>

    <!-- This will check if the user is allowed to read/view. Based on the `tbl_user_access_permission` -->
    @if($permissions->read == '1')
    <div>
        @if($noRecords)
        <div class="pagination-info pt-2">
            @if((int) Auth::user()->user_role_id !== 1)
                @if($hasNoSectorPermissions)
                <div class="card shadow-sm border-0 py-5 text-center my-3">
                    <div class="card-body">
                        <i class="bi bi-shield-lock text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 fw-bold text-dark">No Sector or Sub-Sector Assigned</h5>
                        <p class="text-muted mb-0">Your account has not been assigned to any sector. Document uploads and archive viewing are disabled. Please contact the administrator.</p>
                    </div>
                </div>
                @elseif(!empty($filterYear) || !empty($filterSemester) || !empty($search))
                <p class="text-center text-muted py-4"><i class="bi bi-folder-x me-1"></i> No documents found matching the selected filters.</p>
                @else
                <p class="text-center text-muted"><i class="bi bi-folder-x me-1"></i> You have not submitted any documents yet. Click <strong>"Add"</strong> above to submit a document.</p>
                @endif
            @else
                @if($hasNoSectorPermissions)
                <div class="card shadow-sm border-0 py-5 text-center my-3">
                    <div class="card-body">
                        <i class="bi bi-shield-lock text-warning" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 fw-bold text-dark">No Sector or Sub-Sector Assigned</h5>
                        <p class="text-muted mb-0">Your administrator account does not have permission for any sector. Document uploads and archive viewing are disabled. Please configure sector access in User Administration.</p>
                    </div>
                </div>
                @elseif($noFilterSectorSelected)
                <div class="card shadow-sm border-0 py-5 text-center my-3">
                    <div class="card-body">
                        <i class="bi bi-funnel text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 fw-bold text-dark">Please Select a Sector / Sub-Sector</h5>
                        <p class="text-muted mb-0">Choose a sector and sub-sector from the filters above to view uploaded documents.</p>
                    </div>
                </div>
                @else
                <div class="card shadow-sm border-0 py-5 text-center my-3">
                    <div class="card-body">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h6 class="mt-2 text-muted">No uploaded documents found for the selected sector / sub-sector.</h6>
                    </div>
                </div>
                @endif
            @endif
        </div>
        @else
        <div class="pagination-info pb-2">
            Page {{ $currentPage }} out of {{ $totalPages }}, Total Records: {{ $totalRecords }}
        </div>
        <div class="table-responsive" wire:loading.class="opacity-50">
            <table class="table table-bordered table-hover align-middle">
                <thead class="custom-thead-document-archive text-center">
                    <tr>
                        <th scope="col" style="width: 50px;"></th>
                        <th scope="col" style="width: 140px;">Status</th>
                        <th scope="col" style="width: 80px;">Year</th>
                        <th scope="col" style="width: 95px;">Sem</th>
                        <th scope="col">Sector</th>
                        <th scope="col">Sub Sector</th>
                        <th scope="col">Document Type</th>
                        <th scope="col">Description</th>
                        <th scope="col">Source</th>
                        @if($this->isAdministrator())
                        <th scope="col" style="width: 80px;">Delete</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr wire:key="{{ $item->trans_id }}">
                        <th scope="row" class="text-center py-3">
                            <span wire:click="edit('{{ $item->trans_id }}')" class="cursor-pointer">
                                <img src="{{ asset('assets/img/notepad2-linear.svg') }}" alt="File" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                            </span>
                        </th>
                        <td class="text-center align-middle">
                            @if($item->approval_status === 'approved')
                            <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                <i class="bi bi-check-circle me-1"></i>Approved
                            </span>
                            @elseif($item->approval_status === 'declined')
                            <span class="badge bg-danger text-white border border-danger px-2 py-1" @if($item->decline_reason) title="Declined: {{ $item->decline_reason }}" @endif>
                                <i class="bi bi-exclamation-circle me-1"></i>Returned for Revision
                            </span>
                            @if($item->decline_reason && ($item->create_user === Auth::user()->name || $item->create_user === Auth::user()->user_id))
                            <div class="text-danger small mt-1 text-truncate" style="max-width: 140px; font-size: 0.72rem;" title="{{ $item->decline_reason }}">
                                <strong>Reason:</strong> {{ $item->decline_reason }}
                            </div>
                            @endif
                            @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">
                                <i class="bi bi-clock me-1"></i>Pending Approval
                            </span>
                            @endif
                        </td>
                        <td class="text-center align-middle fw-medium">{{ $item->year ?? '—' }}</td>
                        <td class="text-center align-middle fw-medium">{{ $item->semester_label ?? '—' }}</td>
                        <td>{{ $item['sector_name'] }}</td>
                        <td>{{ $item['sub_sector_name'] }}</td>
                        <td>{{ $item['doctype_desc'] }}</td>
                        <td>{{ $item['document_desc'] }}</td>
                        <td>{{ $item['document_source'] }}</td>
                        @if($this->isAdministrator())
                        <td class="text-center align-middle py-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    wire:click="openDeleteDocumentConfirm('{{ $item->trans_id }}')"
                                    title="Delete Document">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $data->links('vendor.livewire.custom-pagination-links') }}
        </div>
        @endif
    </div>
    @endif

    <!-- MODAL -->
    @if($isModalOpen)
    <div class="modal fade show" id="ExtralargeModal" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Document Archive
                        @if($currentApprovalStatus === 'declined')
                        <span class="badge bg-danger ms-2 fs-6"><i class="bi bi-exclamation-octagon me-1"></i>Revision Mode</span>
                        @elseif($currentApprovalStatus === 'pending')
                        <span class="badge bg-warning text-dark ms-2 fs-6"><i class="bi bi-clock me-1"></i>Pending Approval</span>
                        @elseif($currentApprovalStatus === 'approved')
                        <span class="badge bg-success ms-2 fs-6"><i class="bi bi-check-circle me-1"></i>Approved</span>
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="{{ $Posttransid ? 'update' : 'store' }}" class="row g-3" data-bitwarden-watching="1">
                        @if($currentApprovalStatus === 'declined')
                        <div class="col-12">
                            <div class="alert alert-danger border-danger p-3 mb-2 shadow-sm">
                                <h6 class="fw-bold text-danger mb-1">
                                    <i class="bi bi-exclamation-octagon-fill me-2"></i>Document Returned for Revision
                                </h6>
                                <p class="mb-2 small text-dark">
                                    The administrator declined this document with the following feedback:
                                </p>
                                <div class="bg-white p-3 rounded border border-danger-subtle text-danger fw-semibold mb-2">
                                    <i class="bi bi-chat-left-quote me-2"></i>{{ $currentDeclineReason ?? 'No reason provided.' }}
                                </div>
                                <small class="text-muted d-block">
                                    Please make the necessary corrections to the fields or files below, then click <strong>"Resubmit for Approval"</strong>.
                                </small>
                            </div>
                        </div>
                        @endif
                        @if($allowedSectors->isEmpty())
                        <div class="col-12">
                            <div class="alert alert-danger d-flex align-items-center mb-2" role="alert">
                                <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i>
                                <div>
                                    <strong>Upload Disabled:</strong> No sector or sub-sector has been assigned to your account. You cannot upload documents until sector access is granted by the system administrator.
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-12">
                            @error('selectedSector')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-addon1">Sector:</span>
                                <select wire:model.change="selectedSector" class="form-select" aria-label="Default select example">
                                    <option selected="">Select...</option>
                                    @foreach($allowedSectors as $sectors)
                                    <option value="{{ $sectors->sector_id }}">{{ $sectors->sector_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            @error('selectedSubSector')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-addon1">Sub Sector:</span>
                                <select wire:model.change="selectedSubSector" class="form-select" aria-label="Default select example" @if(empty($selectedSector)) disabled @endif>
                                    <option selected="">Select...</option>
                                    @foreach($subSectors as $subSector)
                                    <option value="{{ $subSector->sub_sector_id }}">{{ $subSector->sub_sector_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            @error('selectedDocumentType')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-addon1">Document Type:</span>
                                <select wire:model.change="selectedDocumentType" class="form-select" aria-label="Default select example" @if(empty($selectedSubSector)) disabled @endif>
                                    <option selected="">Select...</option>
                                    @foreach($documentTypes as $documentType)
                                    <option value="{{ $documentType->doctype_code }}">{{ $documentType->doctype_desc }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            @error('document_desc')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text">Description:</span>
                                <textarea wire:model="document_desc" class="form-control" aria-label="With textarea"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            @error('document_source')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-addon1">Source:</span>
                                {{-- Source is automatically set from the logged-in user's office and is read-only --}}
                                @php
                                    // On edit, resolve the stored department ID to a name; on add use the current user's dept
                                    $displaySource = $userDeptName;
                                    if ($Posttransid && $document_source) {
                                        $storedDept = \App\Models\TblRefDepartment::where('department_id', $document_source)->value('department_name');
                                        $displaySource = $storedDept ?? $document_source;
                                    }
                                @endphp
                                <input type="text"
                                       class="form-control bg-light text-dark"
                                       value="{{ $displaySource }}"
                                       readonly
                                       tabindex="-1"
                                       style="cursor: default; background-color: #f8f9fa !important;">
                                {{-- Hidden input keeps the value bound to Livewire --}}
                                <input type="hidden" wire:model="document_source">
                            </div>
                        </div>

                        <hr>

                        <!-- Save to tbl_document_data -->
                        <!-- Show upload button when edit mode and add new document description and file -->
                        @if($Posttransid)
                        <!-- END INPUT FIELD FOR UPLOAD ANOTHER FILE -->

                        <!-- Status Alert for file operations inside modal -->
                        @if(session('file_status'))
                        <div class="col-12">
                            <div class="alert alert-success alert-dismissible fade show my-2" role="alert">
                                <i class="bi bi-check-circle me-1"></i>
                                {{ session('file_status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="modal-title mb-0">Uploaded Documents</h5>
                            <!-- This will check if the user is allowed to create/upload a file. Based on the `tbl_user_access_permission` -->
                            @if($permissions->create == '1')
                            <button type="button" class="btn btn-sm btn-success" wire:click="{{ $isUploadFieldOpen ? 'hideUploadInputField' : 'addfile'}}" wire:loading.class="opacity-50" wire:target="{{ $isUploadFieldOpen ? 'hideUploadInputField' : 'addfile'}}">
                                <i class="bi bi-plus-circle me-1"></i> Upload Another File
                            </button>
                            @endif
                        </div>

                        <!-- INPUT FIELD FOR UPLOAD ANOTHER FILE -->
                        @if($isUploadFieldOpen)
                        <div class="modal fade show" id="uploadNewFileModal" aria-labelledby="uploadNewFileModalTitle" style="display: block; background-color: rgba(1, 76, 143, 0.35); z-index: 1060;" aria-modal="true" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content shadow-lg border-0">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold" id="uploadNewFileModalTitle">
                                            <i class="bi bi-cloud-arrow-up text-success me-2"></i>Upload Document
                                        </h5>
                                        <button type="button" class="btn-close" aria-label="Close" wire:click="hideUploadInputField"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Document Description <span class="text-danger">*</span></label>
                                            <textarea wire:model="Newdocument_desc2" class="form-control @error('Newdocument_desc2') is-invalid @enderror" rows="3" placeholder="Enter document description"></textarea>
                                            @error('Newdocument_desc2')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">PDF File <span class="text-danger">*</span></label>
                                            <input wire:model="NewpdfFile" class="form-control @error('NewpdfFile') is-invalid @enderror" type="file" id="formFileNew" accept="application/pdf">
                                            @error('NewpdfFile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div wire:loading wire:target="NewpdfFile" class="text-primary small mt-2">
                                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading file, please wait...
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <input wire:model="Posttransid" type="hidden" value="{{ $Posttransid }}">
                                        <button type="button" class="btn btn-secondary" wire:click="hideUploadInputField" wire:loading.class="opacity-50" wire:target="hideUploadInputField">Cancel</button>
                                        <button type="button" class="btn btn-success" wire:click="addNewPdf" wire:loading.attr="disabled" wire:target="NewpdfFile, addNewPdf">
                                            <span wire:loading wire:target="addNewPdf" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                            <i class="bi bi-upload me-1"></i> Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!--END INPUT FIELD FOR UPLOAD ANOTHER FILE -->

                        <!-- MODAL FOR EDITING / UPDATING AN EXISTING UPLOADED FILE -->
                        @if($isEditFileModalOpen)
                        <div class="modal fade show" id="editFileModal" tabindex="-1" style="display: block; background-color: rgba(1, 76, 143, 0.35); z-index: 1060;" aria-modal="true" role="dialog">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content shadow-lg border-0">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-pencil-square text-primary me-2"></i>Update Uploaded Document
                                        </h5>
                                        <button type="button" class="btn-close" wire:click="closeEditFileModal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <!-- Current File Info Card -->
                                        <div class="card bg-light border-0 mb-3">
                                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $editFileName }}</div>
                                                        @if(!empty($editFileSize))
                                                        <small class="text-muted">{{ number_format($editFileSize / 1024, 1) }} KB</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div>
                                                    <a href="{{ route('preview-pdf', ['trans_id' => $editFileTransId]) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-eye me-1"></i> Preview Current
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Edit Description -->
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Document Description <span class="text-danger">*</span>
                                            </label>
                                            <textarea wire:model="editFileDocDesc" class="form-control @error('editFileDocDesc') is-invalid @enderror" rows="3" placeholder="Enter document description"></textarea>
                                            @error('editFileDocDesc')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Replace File (Optional) -->
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">
                                                Replace PDF File <span class="badge bg-secondary-subtle text-secondary fw-normal">Optional</span>
                                            </label>
                                            <input wire:model="editNewPdfFile" class="form-control @error('editNewPdfFile') is-invalid @enderror" type="file" id="editNewPdfFileInput" accept="application/pdf">
                                            <div class="form-text text-muted">
                                                Leave empty to keep the current file, or choose a new PDF to replace it.
                                            </div>
                                            @error('editNewPdfFile')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <div wire:loading wire:target="editNewPdfFile" class="text-primary small mt-2">
                                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading replacement file, please wait...
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" wire:click="closeEditFileModal" wire:loading.class="opacity-50" wire:target="closeEditFileModal">
                                            Cancel
                                        </button>
                                        <button type="button" class="btn btn-primary" wire:click="updateUploadedFile" wire:loading.attr="disabled" wire:target="editNewPdfFile, updateUploadedFile">
                                            <span wire:loading wire:target="updateUploadedFile" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                            <i class="bi bi-check-lg me-1"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- END MODAL FOR EDITING / UPDATING AN EXISTING UPLOADED FILE -->

                        <!-- Confirm DELETE PDF MODAL -->
                        @if($alertPDFdelete)
                        <div class="modal fade show" id="deleteFileModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block; background-color: rgba(1, 76, 143, 0.35); z-index: 1060;">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content shadow-lg border-0">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title fw-bold">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" aria-label="Close" wire:click="closealertDelete"></button>
                                    </div>
                                    <div class="modal-body text-center p-4">
                                        <img src="{{ asset('assets/img/warning.svg') }}" alt="Warning" style="width: 80px; height: 80px;" class="mb-3">
                                        <h6 class="fw-bold">Are you sure you want to delete this uploaded file?</h6>
                                        <p class="text-muted small mb-0">This file will be removed from this document archive.</p>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" wire:click="closealertDelete" wire:loading.class="opacity-50">Cancel</button>
                                        <button type="button" class="btn btn-danger" wire:click="deletePDF">
                                            <i class="bi bi-trash me-1"></i> Delete File
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- END Confirm DELETE PDF MODAL -->

                        @if($pdfFileInfo)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="custom-thead-document-archive text-center">
                                    <tr>
                                        <th scope="col">Document Description</th>
                                        <th scope="col">File Details</th>
                                        <th scope="col" style="width: 80px;">Preview</th>
                                        <th scope="col" style="width: 80px;">Download</th>
                                        <!-- Check if user has update permission to change or edit file -->
                                        @if($permissions->update == '1')
                                        <th scope="col" style="width: 110px;">Change / Edit</th>
                                        @endif
                                        <!-- Check if user has delete permission -->
                                        @if($permissions->delete == '1')
                                        <th scope="col" style="width: 80px;">Delete</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pdfFileInfo as $file)
                                    <tr wire:key="{{ $file['trans_id'] }}">
                                        <!-- Description -->
                                        <td class="fw-semibold">
                                            {{ $file['document_desc'] ?? 'No description provided.' }}
                                        </td>
                                        <!-- File Name & Size -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                                <div>
                                                    <div class="fw-medium text-break">{{ $file['file_name'] }}</div>
                                                    @if(!empty($file['file_size']))
                                                    <small class="text-muted">{{ number_format($file['file_size'] / 1024, 1) }} KB</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Preview -->
                                        <td class="text-center py-2">
                                            <a href="{{ route('preview-pdf', ['trans_id' => $file['trans_id']]) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Preview File">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                        <!-- Download -->
                                        <td class="text-center py-2">
                                            <a href="{{ route('download', ['trans_id' => $file['trans_id']]) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Download File">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                        <!-- Edit / Change File -->
                                        @if($permissions->update == '1')
                                        <td class="text-center py-2">
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark" wire:click="editUploadedFile('{{ $file['trans_id'] }}')" title="Change or Update File">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </button>
                                        </td>
                                        @endif
                                        <!-- Delete -->
                                        @if($permissions->delete == '1')
                                        <td class="text-center py-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="openalertDelete('{{ $file['trans_id'] }}')" title="Delete File">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-light text-center border py-4 my-2">
                            <i class="bi bi-file-earmark-x text-muted fs-3 d-block mb-1"></i>
                            <span class="text-muted">No files attached to this document. Click "Upload Another File" to add one.</span>
                        </div>
                        @endif
                        @else
                        <h5 class="modal-title">Upload Document</h5>
                        <div class="col-12">
                            @error('document_desc2')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-addon1">Document Description:</span>
                                <textarea wire:model="document_desc2" class="form-control" aria-label="With textarea"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            @error('pdfFile')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <input wire:model="pdfFile" class="form-control @error('pdfFile') is-invalid @enderror" type="file" id="formFile" accept="application/pdf">
                            </div>
                            <div wire:loading wire:target="pdfFile" class="text-primary small">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Uploading file, please wait...
                            </div>
                        </div>
                        @endif
                        <!-- End Save to tbl_document_data -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal" wire:loading.class="opacity-50" wire:target="closeModal">
                        Close
                    </button>

                    <!-- Check if user is allowed to create (when new) or update (when editing) -->
                    @if(($Posttransid && $permissions->update == '1') || (!$Posttransid && $permissions->create == '1'))
                    <button wire:click="{{ $Posttransid ? 'update' : 'store' }}" type="button" class="btn {{ $currentApprovalStatus === 'declined' ? 'btn-danger' : 'btn-primary' }}" wire:loading.attr="disabled" wire:target="pdfFile, store, update" @if($allowedSectors->isEmpty()) disabled @endif>
                        <span wire:loading wire:target="{{ $Posttransid ? 'update' : 'store' }}" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        @if($Posttransid)
                            @if($currentApprovalStatus === 'declined')
                            <i class="bi bi-send-check me-1"></i> Resubmit for Approval
                            @else
                            <i class="bi bi-check-lg me-1"></i> Update
                            @endif
                        @else
                            <i class="bi bi-plus-lg me-1"></i> Submit
                        @endif
                    </button>
                    @endif

                </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    <!-- END MODAL -->

    <!-- CONFIRM DELETE DOCUMENT MODAL (Administrator only) -->
    @if($alertDocumentDelete && $this->isAdministrator())
    <div class="modal fade show" id="deleteDocumentModal" tabindex="-1" aria-modal="true" role="dialog"
         style="display: block; background-color: rgba(1, 76, 143, 0.5); z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete Document
                    </h5>
                    <button type="button" class="btn-close btn-close-white" aria-label="Close"
                            wire:click="closeDeleteDocumentConfirm"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="{{ asset('assets/img/warning.svg') }}" alt="Warning"
                         style="width: 80px; height: 80px;" class="mb-3">
                    <h6 class="fw-bold">Are you sure you want to delete this document?</h6>
                    <p class="text-muted small mb-0">
                        This will permanently remove the document archive entry and
                        <strong>all attached files</strong>. This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary"
                            wire:click="closeDeleteDocumentConfirm"
                            wire:loading.class="opacity-50">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger"
                            wire:click="deleteDocument"
                            wire:loading.attr="disabled"
                            wire:target="deleteDocument">
                        <span wire:loading wire:target="deleteDocument"
                              class="spinner-border spinner-border-sm me-1" role="status"></span>
                        <i class="bi bi-trash me-1"></i> Delete Document
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- END CONFIRM DELETE DOCUMENT MODAL -->

</div>