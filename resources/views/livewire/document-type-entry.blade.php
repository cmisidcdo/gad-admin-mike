<div>
    <!-- CMS MENU ID: 4 - archive page -->
    @php
    $permissions = App\Models\TblUserAccessPermission::where('id_user', auth()->user()->id)->where('id_cms_menu', '4')->first();
    @endphp

    <h5 class="card-title">Document Type Entry</h5>

    <!-- <div class="d-flex align-items-center">
        <div class="input-group mb-3">
            <span class="input-group-text fw-medium" id="basic-addon1" style="background-color: #014C8F26;">Search</span>
            <input wire:model.live.debounce.300ms="search" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1" wire:model="query">
        </div>
    </div> -->
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
                <div class="col-lg-4 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text fw-medium" id="basic-addon1" style="background-color: #014C8F26;">
                            <i class="bi bi-search me-1"></i> Search
                        </span>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search document types...">
                    </div>
                </div>

                <!-- Sector Filter -->
                <div class="col-lg-3 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text fw-medium" style="background-color: #014C8F26;">Sector</span>
                        <select wire:model.live="filterSector" class="form-select" @if($allowedSectors->isEmpty()) disabled @endif>
                            <option value="">{{ $allowedSectors->isEmpty() ? 'No Sector Assigned' : 'Choose Sector...' }}</option>
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
                            <option value="">{{ empty($filterSector) ? 'Select Sector first...' : 'All Allowed Sub-Sectors' }}</option>
                            @foreach($filterSubSectors as $sub)
                            <option value="{{ $sub->sub_sector_id }}">{{ $sub->sub_sector_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- This will check if the user is allowed to create. Based on the `tbl_user_access_permission` -->
                @if($permissions->create == '1')
                <div class="col-lg-2 col-md-2 text-end">
                    <button wire:click="add" class="btn btn-primary w-100" @if($allowedSectors->isEmpty()) disabled title="No sector assigned" @endif>
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
            @if($hasNoSectorPermissions)
            <div class="card shadow-sm border-0 py-5 text-center my-3">
                <div class="card-body">
                    <i class="bi bi-shield-lock text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 fw-bold text-dark">No Sector or Sub-Sector Assigned</h5>
                    <p class="text-muted mb-0">Your account does not have permission for any sector. Document type viewing and entry are disabled.</p>
                </div>
            </div>
            @elseif($noFilterSectorSelected)
            <div class="card shadow-sm border-0 py-5 text-center my-3">
                <div class="card-body">
                    <i class="bi bi-funnel text-primary" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 fw-bold text-dark">Please Select a Sector / Sub-Sector</h5>
                    <p class="text-muted mb-0">Choose a sector and sub-sector from the filters above to view document types.</p>
                </div>
            </div>
            @else
            <div class="card shadow-sm border-0 py-5 text-center my-3">
                <div class="card-body">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h6 class="mt-2 text-muted">No document types found for the selected sector / sub-sector.</h6>
                </div>
            </div>
            @endif
        </div>
        @else
        <div class="pagination-info pb-2">
            Page {{ $currentPage }} out of {{ $totalPages }}, Total Records: {{ $totalRecords }}
        </div>
        <div class="table-responsive" wire:loading.class="opacity-50">
            <table class="table table-bordered table-hover">
                <thead class="custom-thead-document-archive">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Sector</th>
                        <th scope="col">Sub Sector</th>
                        <th scope="col">Document Type</th>
                        <th scope="col">Active</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr wire:key="{{ $item->doctype_code }}" class="text-center">
                        <th scope="row" class="text-center py-3">
                            <span wire:click="edit('{{ $item->doctype_code }}')" class="cursor-pointer">
                                <img src="{{ asset('assets/img/edit.svg') }}" alt="File" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                            </span>
                        </th>
                        <td>{{ $item['sector_name'] }}</td>
                        <td>{{ $item['sub_sector_name'] }}</td>
                        <td>{{ $item['doctype_desc'] }}</td>
                        <td>{{ $item['is_active'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $data->links('vendor.livewire.custom-pagination-links') }}
        </div>
        @endif
    </div>
    @endif

    @if($isModalOpen)
    <!-- MODAL -->
    <div class="modal fade show" id="ExtralargeModal" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Document Type Entry</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- <form class="row g-3" data-bitwarden-watching="1" wire:submit="store"> -->
                    <form wire:submit="{{ $PostdoctypeCode ? 'update' : 'store' }}" class="row g-3" data-bitwarden-watching="1">
                        @if($allowedSectors->isEmpty())
                        <div class="col-12">
                            <div class="alert alert-danger d-flex align-items-center mb-2" role="alert">
                                <i class="bi bi-exclamation-octagon-fill fs-4 me-2"></i>
                                <div>
                                    <strong>Entry Disabled:</strong> No sector or sub-sector has been assigned to your account. You cannot create or update document types until sector access is granted.
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
                            @error('documentType')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text">Document Type:</span>
                                <textarea wire:model="documentType" class="form-control" aria-label="With textarea"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            @error('status')<span class="badge border-danger border-1 text-danger">{{ $message }}</span>@enderror
                            <div class="input-group mb-2">
                                <span class="input-group-text" id="basic-addon1">Status:</span>
                                <select wire:model="status" class="form-select" aria-label="Default select example">
                                    <option value="" selected>Select...</option>
                                    <option value="Y">Yes</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal" wire:loading.class="disabled" wire:target="{{ $PostdoctypeCode ? 'update' : 'store' }}">
                        Close
                    </button>

                    <!-- This will check if the user is allowed to update. Based on the `tbl_user_access_permission` -->
                    @if($permissions->update == '1')
                    <button wire:click="{{ $PostdoctypeCode ? 'update' : 'store' }}" type="submit" class="btn btn-primary" @if($allowedSectors->isEmpty()) disabled @endif>
                        {{ $PostdoctypeCode ? 'Update' : 'Submit' }}
                    </button>
                    @endif

                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END MODAL -->
    @endif
</div>