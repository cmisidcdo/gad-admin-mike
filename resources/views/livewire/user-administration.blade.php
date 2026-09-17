<div>
    <!-- CMS MENU ID: 5 - User Administration page -->
    @php
    $permissions = App\Models\TblUserAccessPermission::where('id_user', auth()->user()->id)->where('id_cms_menu', '5')->first();
    @endphp

    <h5 class="card-title">User Administration</h5>

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
        <div class="col-sm-8 d-flex align-items-center">
            <div class="input-group mb-3">
                <span class="input-group-text fw-medium" id="basic-addon1" style="background-color: #014C8F26;">Search</span>
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control" aria-label="Username" aria-describedby="basic-addon1" wire:model="query">
            </div>
        </div>
        <div class="col-sm-2 d-flex align-items-center">
            <div class="input-group mb-3">

            </div>
        </div>

        <!-- This will check if the user is allowed to create. Based on the `tbl_user_access_permission` -->
        @if($permissions->create == '1')
        <div class="col-sm-2 d-flex align-items-center">
            <div class="input-group mb-3 justify-content-end">
                <button class="btn btn-primary" wire:click="addModal">Add</button>
            </div>
        </div>
        @endif

    </div>

    <!-- This will check if the user is allowed to read/view. Based on the `tbl_user_access_permission` -->
    @if($permissions->read == '1')
    <div>
        @if($noRecords)
        <div class="pagination-info pt-4">
            <p class="text-center">No records found.</p>
        </div>
        @else
        <div class="pagination-info pb-2">
            Page {{ $currentPage }} out of {{ $totalPages }}, Total Records: {{ $totalRecords }}
        </div>
        <div class="table-responsive" wire:loading.class="opacity-50" wire:target="edit, search">
            <table class="table table-bordered table-hover">
                <thead class="custom-thead-document-archive">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">User ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Office / Department</th>
                        <th scope="col">Email Address</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                    <tr class="text-center" wire:key="{{ $item->id }}">
                        <th scope="row" class="py-3" style="width: 70px;">
                            <a href="#" wire:click.prevent="edit({{ $item->id }})">
                                <img src="{{ asset('assets/img/user-edit.svg') }}" alt="File" class="img-fluid" style="max-width: 100px; max-height: 100px;">
                            </a>
                        </th>
                        <td>{{ $item['user_id'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-start">
                            {{ $item->department->department_name ?? $item->office ?? 'N/A' }}
                        </td>
                        <td>{{ $item['email'] ?? 'N/A' }}</td>
                        <td>
                            {{ $item['is_active'] === 'Y' ? 'Active' : 'Inactive' }}
                        </td>
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
    @if($addUser)
    <div class="modal fade show" id="basicModal" tabindex="-1" style="display: block; background-color: rgba(1, 76, 143, 0.2);" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $Postid ? 'Edit ' : 'Add ' }}User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeaddModal"></button>
                </div>
                <div class="modal-body">
                    @if(session('userPermissionStatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ session('userPermissionStatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <form wire:submit="{{ $Postid ? 'update' : 'store' }}">
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">User ID:</span>
                            <input type="text" wire:model="user_id" class="form-control @error('user_id') is-invalid @enderror" aria-label="User ID" aria-describedby="basic-addon1" value="{{ old('user_id') }}" autocomplete="user_id">
                            @error('user_id')<span class="invalid-feedback" role="alert">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Username:</span>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" aria-label="Username" value="{{ old('name') }}" aria-describedby="basic-addon1">
                            @error('name')<span class="invalid-feedback" role="alert">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon-email">Email Address:</span>
                            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" aria-label="Email Address" aria-describedby="basic-addon-email" value="{{ old('email') }}" autocomplete="email">
                            @error('email')<span class="invalid-feedback" role="alert">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Office:</span>
                            <select wire:model="office" class="form-select @error('office') is-invalid @enderror" aria-label="Office select">
                                <option value="">Select Department / Office...</option>
                                @foreach($departments as $dept)
                                <option value="{{ $dept->department_id }}">{{ $dept->department_name }}</option>
                                @endforeach
                            </select>
                            @error('office')<span class="invalid-feedback" role="alert">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">User Role:</span>
                            <select wire:model="user_role_id" class="form-select @error('user_role_id') is-invalid @enderror" aria-label="Default select example">
                                <option selected="">Select...</option>
                                @foreach(App\Models\TblUserRole::all() as $role)
                                <option value="{{ $role['user_role_id'] }}">{{ $role['user_role_name'] }}</option>
                                @endforeach
                            </select>
                            @error('user_role_id')<span class="invalid-feedback" role="alert">{{ $message }}</span>@enderror
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1">Status:</span>
                            <select wire:model="status" class="form-select @error('status') is-invalid @enderror" aria-label="Default select example">
                                <option selected="">Select...</option>
                                <option value="Y">Active</option>
                                <option value="N">Inactive</option>
                            </select>
                            @error('status')<span class="invalid-feedback" role="alert">{{ $message }}</span>@enderror
                        </div>
                        @if($Postid)
                        <!-- This will check if the user is allowed to update. Based on the `tbl_user_access_permission` -->
                        @if($permissions->update == '1')
                        <hr>
                        <div class="input-group mb-3">
                            <button class="btn btn-primary" type="button" style="width: 100%;" wire:click="permissionModal({{ $Postid }})" wire:loading.class="opacity-50" wire:target="permissionModal"><i class="ri-user-settings-line me-2"></i>Edit Permission</button>
                        </div>
                        <div class="input-group mb-3">
                            <button class="btn btn-outline-primary" type="button" style="width: 100%;" wire:click="sectorAccessModal({{ $Postid }})" wire:loading.class="opacity-50" wire:target="sectorAccessModal"><i class="ri-building-line me-2"></i>Edit Sector Access</button>
                        </div>
                        @endif
                        @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeaddModal" wire:loading.class="opacity-50" wire:target="closeaddModal">Close</button>

                    <!-- This will check if the user is allowed to update. Based on the `tbl_user_access_permission` -->
                    @if($permissions->update == '1')
                    <button wire:click="{{ $Postid ? 'update' : 'store' }}" type="submit" class="btn btn-primary">
                        {{ $Postid ? 'Update' : 'Save changes' }}
                    </button>
                    @endif

                </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    <!-- END MODAL -->

    <!-- USER PERMISSION MODAL -->
    @if($userPermissionModal)
    <div class="modal fade show" id="ExtralargeModal" tabindex="-1" style="display: block; background-color: rgba(1, 76, 143, 0.2);" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User Permission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closePermissionModal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <div class="mb-2">
                            <h5><strong>Name:</strong> {{ App\Models\User::findOrFail($Postid)->name }}</h5>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead class="custom-thead-document-archive">
                                <tr>
                                    <th scope="col">Module/Forms</th>
                                    <th scope="col">Create</th>
                                    <th scope="col">Read</th>
                                    <th scope="col">Update</th>
                                    <th scope="col">Delete</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($menus as $menu)
                                <tr wire:key="{{ $menu->id }}">
                                    <td scope="row" class="py-3" style="width: 70px;">
                                        {{ $menu['menu_name'] }}
                                    </td>
                                    <td scope="row" class="py-3" style="width: 70px;">
                                        <input type="checkbox" wire:model="selectedPermissions.{{ $menu['id'] }}.create" />
                                    </td>
                                    <td scope="row" class="py-3" style="width: 70px;">
                                        <input type="checkbox" wire:model="selectedPermissions.{{ $menu['id'] }}.read" />
                                    </td>
                                    <td scope="row" class="py-3" style="width: 70px;">
                                        <input type="checkbox" wire:model="selectedPermissions.{{ $menu['id'] }}.update" />
                                    </td>
                                    <td scope="row" class="py-3" style="width: 70px;">
                                        <input type="checkbox" wire:model="selectedPermissions.{{ $menu['id'] }}.delete" />
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closePermissionModal" wire:loading.class="opacity-50" wire:target="closePermissionModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="createOrUpdate_user_permission_data({{ App\Models\User::findOrFail($Postid)->id }})">Update</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- END USER PERMISSION MODAL -->

    <!-- ═══════════════════════════════════════════════════ -->
    <!-- SECTOR ACCESS PERMISSION MODAL                      -->
    <!-- ═══════════════════════════════════════════════════ -->
    @if($sectorPermissionModal)
    <div class="modal fade show" id="sectorAccessModal" tabindex="-1" style="display: block; background-color: rgba(1, 76, 143, 0.2);" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="ri-building-line me-2"></i>Edit Sector Access</h5>
                    <button type="button" class="btn-close" aria-label="Close" wire:click="closeSectorPermissionModal"></button>
                </div>
                <div class="modal-body">
                    @if(session('userPermissionStatus'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ session('userPermissionStatus') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3">
                        <h6><strong>User:</strong> {{ App\Models\User::findOrFail($Postid)->name }}</h6>
                        <p class="text-muted small mb-0">
                            <i class="ri-information-line me-1"></i>
                            Check the sectors and sub-sectors this user is allowed to access.
                            Use <strong>"All Sub-Sectors"</strong> to grant full sector-wide access.
                            If no sector is selected, the user will see <strong>no uploaded data</strong> and cannot upload data.
                        </p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="custom-thead-document-archive">
                                <tr>
                                    <th scope="col" style="width: 25%">Sector</th>
                                    <th scope="col">Sub-Sectors</th>
                                    <th scope="col" style="width: 180px" class="text-center">All Sub-Sectors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allSectors as $sector)
                                @php
                                    $sid = $sector['sector_id'];
                                    $subList = $allSubSectors[$sid] ?? [];
                                    $isAll = !empty($selectedSectorAccess[$sid]['__all']);
                                @endphp
                                <tr wire:key="sector-{{ $sid }}">
                                    <!-- Sector name -->
                                    <td class="fw-semibold align-middle py-3">{{ $sector['sector_name'] }}</td>

                                    <!-- Sub-sector checkboxes -->
                                    <td class="py-3">
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach($subList as $sub)
                                            @php
                                                $ssid = $sub['sub_sector_id'];
                                                $checked = !empty($selectedSectorAccess[$sid][$ssid]);
                                            @endphp
                                            <div class="form-check" wire:key="sub-{{ $ssid }}">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="sub_{{ $ssid }}"
                                                    wire:model="selectedSectorAccess.{{ $sid }}.{{ $ssid }}"
                                                    {{ $isAll ? 'disabled' : '' }}
                                                >
                                                <label class="form-check-label small" for="sub_{{ $ssid }}">
                                                    {{ $sub['sub_sector_name'] }}
                                                </label>
                                            </div>
                                            @endforeach
                                            @if(empty($subList))
                                                <span class="text-muted small fst-italic">No sub-sectors defined.</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- All sub-sectors toggle -->
                                    <td class="text-center align-middle py-3">
                                        <div class="form-check d-inline-block">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="all_{{ $sid }}"
                                                wire:model="selectedSectorAccess.{{ $sid }}.__all"
                                                wire:change="toggleSectorAll('{{ $sid }}')"
                                            >
                                            <label class="form-check-label small" for="all_{{ $sid }}">All</label>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeSectorPermissionModal" wire:loading.class="opacity-50" wire:target="closeSectorPermissionModal">Close</button>
                    <button type="button" class="btn btn-primary" wire:click="saveSectorPermissions" wire:loading.class="opacity-50" wire:target="saveSectorPermissions">
                        <span wire:loading wire:target="saveSectorPermissions" class="spinner-border spinner-border-sm me-1" role="status"></span>
                        Save Sector Access
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- END SECTOR ACCESS PERMISSION MODAL -->

</div>