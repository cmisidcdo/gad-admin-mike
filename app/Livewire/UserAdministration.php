<?php

namespace App\Livewire;

use App\Models\TblCmsMenu;
use App\Models\TblRefDepartment;
use App\Models\TblRefSector;
use App\Models\TblRefSubSector;
use App\Models\TblUserAccessPermission;
use App\Models\TblUserSectorPermission;
use App\Models\User;
use App\Services\UserPermissionService;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserAdministration extends Component
{
    use WithPagination;

    public $search = '';
    public $menus, $userID, $userName;

    // FOR EDIT MODAL
    public $Postid; //We stored the primary key or id in this variable. This is going to be used for edits.

    // Input Fields
    public $user_id, $name, $email, $user_role_id, $office, $status;
    public $selectedPermissions = []; // Stores the selected permissions from blade.
    public $userPermissions = [];

    // SECTOR PERMISSION MODAL state
    public $sectorPermissionModal = false;
    // Holds the nested state: selectedSectorAccess[sector_id][sub_sector_id] = true/false
    public $selectedSectorAccess = [];
    // All sectors and sub-sectors for the modal
    public $allSectors = [];
    public $allSubSectors = []; // keyed by sector_id

    // MODALS, PERMISSION MODAL
    public $addUser = 0, $userPermissionModal = 0;

    // RESET PASSWORD CONFIRM MODAL
    public $resetPasswordConfirmId = null;

    // MODALS
    public function addModal()
    {
        $this->reset('user_id', 'name', 'email', 'user_role_id', 'office', 'status', 'Postid');
        $this->openaddModal();
        $this->resetValidation();
    }
    public function openaddModal()
    {
        $this->addUser = true;
    }
    public function closeaddModal()
    {
        $this->addUser = false;
    }

    // PERMISSION MODAL
    public function permissionModal($id)
    {
        $this->Postid = $id;

        // Fetch user's permissions from the database
        $userPermissions = TblUserAccessPermission::where('id_user', $id)->get()->keyBy('id_cms_menu');
        $menus = TblCmsMenu::all();

        $this->selectedPermissions = [];

        // Populate the selectedPermissions array with the fetched permissions
        foreach ($menus as $menu) {
            if (isset($userPermissions[$menu->id])) {
                $perm = $userPermissions[$menu->id];
                $this->selectedPermissions[$menu->id] = [
                    'create' => (int)$perm->create === 1,
                    'read'   => (int)$perm->read === 1,
                    'update' => (int)$perm->update === 1,
                    'delete' => (int)$perm->delete === 1,
                ];
            } else {
                $this->selectedPermissions[$menu->id] = [
                    'create' => false,
                    'read'   => false,
                    'update' => false,
                    'delete' => false,
                ];
            }
        }

        $this->openPermissionModal();
    }
    public function openPermissionModal()
    {
        $this->userPermissionModal = true;
    }
    public function closePermissionModal()
    {
        $this->reset('selectedPermissions');
        $this->userPermissionModal = false;
    }

    // ─────────────────────────────────────────────────────────
    // SECTOR ACCESS PERMISSION MODAL
    // ─────────────────────────────────────────────────────────

    /**
     * Open the Sector Access modal for the given user.
     * Loads all sectors/sub-sectors and pre-checks existing assignments.
     */
    public function sectorAccessModal($id)
    {
        $this->Postid = $id;

        // Load all sectors and sub-sectors for the UI
        $this->allSectors = TblRefSector::orderBy('sort_order')->get()->toArray();

        $subSectors = TblRefSubSector::orderBy('sector_id')->orderBy('sort_order')->get();
        $this->allSubSectors = [];
        foreach ($subSectors as $ss) {
            $this->allSubSectors[$ss->sector_id][] = $ss->toArray();
        }

        // Pre-load existing sector permission assignments
        $existing = TblUserSectorPermission::where('id_user', $id)->get();
        $this->selectedSectorAccess = [];
        foreach ($existing as $row) {
            if ($row->sub_sector_id === null) {
                // Broad sector-level access — mark "all" toggle
                $this->selectedSectorAccess[$row->sector_id]['__all'] = true;
            } else {
                $this->selectedSectorAccess[$row->sector_id][$row->sub_sector_id] = true;
            }
        }

        $this->sectorPermissionModal = true;
    }

    /**
     * Toggle "all sub-sectors" for a sector. When checked, clears individual sub-sector selections.
     */
    public function toggleSectorAll($sectorId)
    {
        $isAll = $this->selectedSectorAccess[$sectorId]['__all'] ?? false;

        if ($isAll) {
            // Clear all individual sub-sector selections for this sector
            $this->selectedSectorAccess[$sectorId] = ['__all' => true];
        }
        // If unchecked, we leave individual checkboxes as-is so they can be re-selected
    }

    /**
     * Save sector access permissions for the user.
     * Replaces all existing rows for this user with the new selection.
     */
    public function saveSectorPermissions()
    {
        $userId = $this->Postid;

        // Delete all existing sector permission rows for this user
        TblUserSectorPermission::where('id_user', $userId)->delete();

        // Rebuild from selectedSectorAccess
        foreach ($this->selectedSectorAccess as $sectorId => $subMap) {
            $hasAll = !empty($subMap['__all']);

            if ($hasAll) {
                // Grant broad sector-level access (sub_sector_id = NULL)
                TblUserSectorPermission::create([
                    'id_user'      => $userId,
                    'sector_id'    => $sectorId,
                    'sub_sector_id' => null,
                ]);
            } else {
                // Grant individual sub-sector access
                foreach ($subMap as $subSectorId => $checked) {
                    if ($subSectorId === '__all') continue;
                    if ($checked) {
                        TblUserSectorPermission::create([
                            'id_user'       => $userId,
                            'sector_id'     => $sectorId,
                            'sub_sector_id' => $subSectorId,
                        ]);
                    }
                }
            }
        }

        session()->flash('userPermissionStatus', 'Sector access permissions updated successfully.');
        $this->closeSectorPermissionModal();
    }

    public function closeSectorPermissionModal()
    {
        $this->sectorPermissionModal = false;
        $this->reset('selectedSectorAccess', 'allSectors', 'allSubSectors');
    }

    // ─────────────────────────────────────────────────────────
    // ADDING NEW USER
    // ─────────────────────────────────────────────────────────
    public function store()
    {
        $rules = [
            'user_id'       =>          'required',
            'name'          =>          'required',
            'email'         =>          'nullable|email',
            'user_role_id'  =>          'required',
            'office'        =>          'required',
            'status'        =>          'required',
        ];
        $messages = [
            'user_id.required'      =>      'User ID is required.',
            'name.required'         =>      'Name is required.',
            'email.email'           =>      'Please enter a valid email address.',
            'user_role_id.required' =>      'User Role is required',
            'office.required'       =>      'Office / Department is required.',
            'status.required'       =>      'Status is required',
        ];

        $this->validate($rules, $messages);

        // Checks for duplicate user_id
        $user_idExists = User::where('user_id', $this->user_id)->exists();
        if ($user_idExists) {
            $this->addError('user_id', 'Please choose another unique User ID.');
        } else {
            $newUser = User::create([
                'user_id'           =>          $this->user_id,
                'name'              =>          $this->name,
                'email'             =>          $this->email,
                'user_role_id'      =>          $this->user_role_id,
                'office'            =>          $this->office,
                'is_active'         =>          $this->status,
                'password'          =>          Hash::make('password'),
            ]);

            // Automatically assign default permissions based on the user's role
            $this->assignDefaultPermissions($newUser);

            session()->flash('status', 'Added successfully.');

            return redirect()->to('/user-administration');
        }
    }

    /**
     * Assign default module permissions — delegates to UserPermissionService.
     * User role: Archive CRU; all others: none.
     * Admin role: full access to all modules.
     */
    public function assignDefaultPermissions($user)
    {
        UserPermissionService::assignDefault($user);
    }

    // EDIT MODAL
    public function edit($id)
    {
        $item = User::findOrFail($id); // Fetches all information based on $id. Then assign the data to each fields.
        $this->Postid       = $id;
        $this->user_id      = $item->user_id;
        $this->name         = $item->name;
        $this->email        = $item->email;
        $this->user_role_id = $item->user_role_id;
        $this->office       = $item->office;
        $this->status       = $item->is_active;

        $this->resetValidation();
        $this->openaddModal();
    }

    // UPDATE
    public function update()
    {
        $rules = [
            'user_id'       => 'required|unique:users,user_id,' . $this->Postid, // `unique:users,user_id,{$this->Postid}` tells Laravel to check for the uniqueness of user_id in the 'users' table, excluding the current record with the ID $this->Postid. This way, when you update other fields and not the user_id, it won't trigger the uniqueness validation.
            'name'          => 'required',
            'email'         => 'nullable|email',
            'user_role_id'  => 'required',
            'office'        => 'required',
            'status'        => 'required',
        ];

        $messages = [
            'user_id.required'      => 'User ID is required.',
            'user_id.unique'        => 'Please choose another unique User ID.',
            'name.required'         => 'Name is required.',
            'email.email'           => 'Please enter a valid email address.',
            'user_role_id.required' => 'User Role is required',
            'office.required'       => 'Office / Department is required.',
            'status.required'       => 'Status is required',
        ];

        $this->validate($rules, $messages);

        if ($this->Postid) {
            $item = User::findOrFail($this->Postid);
            $item->update([
                'user_id'       => $this->user_id,
                'name'          => $this->name,
                'email'         => $this->email,
                'user_role_id'  => $this->user_role_id,
                'office'        => $this->office,
                'is_active'     => $this->status,
            ]);

            session()->flash('status', 'Updated successfully.');
            $this->closeaddModal();
            $this->reset('user_id', 'name', 'email', 'user_role_id', 'office', 'status', 'Postid');
        }
    }

    // To save the permissions
    public function createOrUpdate_user_permission_data($id)
    {
        // Loop through each menu and save the selected permissions
        foreach ($this->selectedPermissions as $menuId => $permissions) {
            TblUserAccessPermission::updateOrCreate(
                ['id_user' => $id, 'id_cms_menu' => $menuId],
                [
                    'create' => !empty($permissions['create']) ? 1 : 0,
                    'read'   => !empty($permissions['read']) ? 1 : 0,
                    'update' => !empty($permissions['update']) ? 1 : 0,
                    'delete' => !empty($permissions['delete']) ? 1 : 0,
                ]
            );
        }

        session()->flash('userPermissionStatus', 'User Permission updated successfully.');
        $this->closePermissionModal();
        $this->reset('selectedPermissions');
    }

    /**
     * Open the Reset Password confirmation modal (no JS confirm dialog).
     */
    public function confirmResetPassword($id)
    {
        $this->resetPasswordConfirmId = $id;
    }

    /**
     * Cancel / dismiss the Reset Password confirmation modal.
     */
    public function cancelResetPassword()
    {
        $this->resetPasswordConfirmId = null;
    }

    /**
     * Reset the user's password to the default ("password").
     */
    public function resetPassword()
    {
        $user = User::findOrFail($this->resetPasswordConfirmId);
        $user->update([
            'password' => Hash::make('password'),
        ]);

        $this->resetPasswordConfirmId = null;
        session()->flash('status', "Password for {$user->name} ({$user->user_id}) has been reset to default (\"password\").");
    }



    public function render()
    {
        // Fetch all menus. DISPLAYING DATA for the USER PERMISSION MODAL.
        $this->menus = TblCmsMenu::all();

        // Fetch all departments sorted by department_name for the Office dropdown.
        $departments = TblRefDepartment::orderBy('department_name', 'asc')->get();

        // Instantiate a User model and then call the search method, ordered by latest user added
        $data = (new User())->search(User::query(), $this->search)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.user-administration', [
            'departments'   => $departments,
            'data'          => $data,
            'currentPage'   => $data->currentPage(),
            'totalPages'    => $data->lastPage(),
            'totalRecords'  => $data->total(),
            'noRecords'     => $data->isEmpty(),
        ]);
    }

    public function updating()
    {
        $this->resetPage();
    }
}

