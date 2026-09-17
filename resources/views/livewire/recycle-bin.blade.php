<div>
    <!-- CMS MENU ID: 6 - recycle bin page -->
    @php
    $permissions = App\Models\TblUserAccessPermission::where('id_user', auth()->user()->id)->where('id_cms_menu', '6')->first();
    @endphp

    <h5 class="card-title">Recycle Bin</h5>
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
        <div class="col-sm-8 d-flex align-items-center">
            <div class="input-group mb-3">
                <span class="input-group-text fw-medium" id="basic-addon1" style="background-color: #014C8F26;">Search</span>
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1" wire:model="query">
            </div>
        </div>
        <div class="col-sm-4 d-flex align-items-center">
            <div class="input-group mb-3">

            </div>
        </div>

        <!-- This will check if the user is allowed to read/view. Based on the `tbl_user_access_permission` -->
        @if($permissions->read == '1')
        @if($noRecords)
        <div class="pagination-info pt-4">
            <p class="text-center">No records found.</p>
        </div>
        @else
        <div class="pagination-info pb-2">
            Page {{ $currentPage }} out of {{ $totalPages }}, Total Records: {{ $totalRecords }}
        </div>
        <div class="table-responsive" wire:loading.class="opacity-50" wire:target="restore, search">
            <table class="table table-bordered table-hover">
                <thead class="custom-thead-document-archive">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Document Description</th>
                        <th scope="col">File Name</th>
                        <th scope="col">User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr class="text-center" wire:key="{{ $item->id }}">
                        <th scope="row" class="py-3" style="width: 70px;">
                            @if($permissions->update == '1')
                            <a href="#" wire:click.prevent="restore('{{ $item->id }}')">
                                <img src="{{ asset('assets/img/arrow-left.svg') }}" alt="File" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                            </a>
                            @else
                            <a href="#">
                                <img src="{{ asset('assets/img/arrow-left-gray.svg') }}" alt="File" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                            </a>
                            @endif
                        </th>
                        <td>{{ $item['document_desc'] }}</td>
                        <td>{{ $item['file_name'] }}</td>
                        <td>
                            {{ $item['upload_user'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $data->links('vendor.livewire.custom-pagination-links') }}
        </div>
        @endif
        @endif

        @if($rModal)
        <div class="modal fade show" id="exampleModalCenter" tabindex="-1" aria-labelledby="exampleModalCenterTitle" aria-modal="true" role="dialog" style="display: block; background-color: rgba(1, 76, 143, 0.2);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Warning!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeRestoreModal" wire:loading.class="opacity-50" wire:target="closeRestoreModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <img src="{{ asset('assets/img/message-question.svg') }}" alt="Loading..." style="width: 100px; height: 100px;">
                            </div>
                            <div class="col text-center mt-3">
                                <p>Are you sure you want to proceed?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" value="{{ $Posttrans_id }}">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeRestoreModal" wire:loading.class="opacity-50" wire:target="closeRestoreModal">Cancel</button>
                        <button type="button" class="btn btn-primary" wire:click="updateRestore">
                            Restore
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>