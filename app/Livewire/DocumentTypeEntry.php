<?php

namespace App\Livewire;

use App\Models\TblRefDocumentTypes;
use App\Models\TblRefSector;
use App\Models\TblRefSubSector;
use App\Models\TblUserSectorPermission;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str; //THIS IS FOR THE str::random()
use Illuminate\Validation\Rule;

class DocumentTypeEntry extends Component
{
    use WithPagination;

    public $search = ''; // Search

    public $isModalOpen = 0; // Modal

    // Table Filter Properties
    public $filterSector = '';
    public $filterSubSector = '';

    // FORM FIELDS
    // Dynamic Select
    public $selectedSector;
    public $selectedSubSector;
    // End Dynamic Select

    public $documentType;
    public $status;
    // END FORM FIELDS

    public $PostdoctypeCode; // This is used for edit. We stored the primary key or id in this variable.

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterSector()
    {
        $this->filterSubSector = '';
        $this->resetPage();
    }

    public function updatedFilterSubSector()
    {
        $this->resetPage();
    }

    // MODAL ---------------------------------------------------
    public function add()
    {
        $this->reset('selectedSector', 'selectedSubSector', 'documentType', 'status', 'PostdoctypeCode');

        // Pre-fill from active filter
        if (!empty($this->filterSector)) {
            $this->selectedSector = $this->filterSector;
            if (!empty($this->filterSubSector)) {
                $this->selectedSubSector = $this->filterSubSector;
            }
        }

        $this->openModal();
        $this->resetValidation();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }
    // END MODAL -----------------------------------------------

    // POST ADD
    public function store()
    {
        // VALIDATION
        $rules = [
            'selectedSector'    => 'required',
            'selectedSubSector' => 'required',
            'documentType'      => 'required',
            'status'            => 'required',
        ];

        $messages = [
            'selectedSector.required'       => 'Sector is required.',
            'selectedSubSector.required'    => 'Sub Sector is required.',
            'documentType.required'         => 'Document type is required.',
            'status.required'               => 'Status is required.',
        ];
        // END VALIDATION

        $this->validate($rules, $messages);

        // Security check: ensure user has permission for the selected sector and sub-sector
        if (!$this->hasSectorAccess($this->selectedSector, $this->selectedSubSector)) {
            $this->addError('selectedSector', 'You are not authorized to add document types for this sector or sub-sector.');
            return;
        }

        // Generate random letters and numbers for doctype_code
        $timestamp = now()->timestamp;
        $randomString = Str::random(32);
        $doctypeCode = $timestamp . $randomString;

        TblRefDocumentTypes::create([
            'doctype_code'  =>      $doctypeCode,
            'sector'        =>      $this->selectedSector,
            'sub_sector'    =>      $this->selectedSubSector,
            'doctype_desc'  =>      $this->documentType,
            'is_active'     =>      $this->status,
        ]);

        session()->flash('status', 'Added successfully.');

        return redirect()->to('/reference');
    }
    // END POST ADD

    //POST EDIT - Shows/Fetch data based on the primary key. By default, laravel will look for `id`. You can modify the primary key by initializing `protected $primaryKey = 'doctype_code';`.
    public function edit($doctype_code)
    {
        $item = TblRefDocumentTypes::findOrFail($doctype_code);

        if (!$this->hasSectorAccess($item->sector, $item->sub_sector)) {
            abort(403, 'Unauthorized access to this document type.');
        }

        $this->PostdoctypeCode = $doctype_code; // We stored the $doctype_code to $PostdoctypeCode. Going back to the modal, other information based on $doctype_code will be fetched to the input fields in the modal.
        $this->selectedSector = $item->sector;
        $this->selectedSubSector = $item->sub_sector;
        $this->documentType = $item->doctype_desc;
        $this->status = $item->is_active;

        $this->resetValidation();
        $this->openModal();
    }
    //END POST EDIT

    public function update()
    {
        // VALIDATION
        $rules = [
            'selectedSector'    => 'required',
            'selectedSubSector' => 'required',
            'status'            => 'required',
            'documentType'      => 'required',
        ];

        $messages = [
            'selectedSector.required'       => 'Sector is required.',
            'selectedSubSector.required'    => 'Sub Sector is required.',
            'status.required'               => 'Status is required.',
            'documentType.required'         => 'Document is required',
        ];

        $this->validate($rules, $messages);

        // Security check: ensure user has permission for the selected sector and sub-sector
        if (!$this->hasSectorAccess($this->selectedSector, $this->selectedSubSector)) {
            $this->addError('selectedSector', 'You are not authorized to assign this sector or sub-sector.');
            return;
        }

        if ($this->PostdoctypeCode) {
            $item = TblRefDocumentTypes::findOrFail($this->PostdoctypeCode);

            if (!$this->hasSectorAccess($item->sector, $item->sub_sector)) {
                abort(403, 'Unauthorized access to this document type.');
            }

            $item->update([
                'sector' => $this->selectedSector,
                'sub_sector' => $this->selectedSubSector,
                'doctype_desc' => $this->documentType,
                'is_active' =>  $this->status,
            ]);

            session()->flash('status', 'Updated successfully.');
            $this->closeModal();
            $this->reset('selectedSector', 'selectedSubSector', 'documentType', 'status', 'PostdoctypeCode');
        }
    }

    /**
     * Returns the sector permissions for the currently logged-in user.
     * Returns an empty collection if no permissions assigned (no access).
     */
    protected function getUserSectorPermissions()
    {
        $userId = auth()->id();
        return TblUserSectorPermission::where('id_user', $userId)->get();
    }

    /**
     * Check if the authenticated user has access to a given sector and (optionally) sub-sector.
     */
    public function hasSectorAccess($sectorId, $subSectorId = null): bool
    {
        $permissions = $this->getUserSectorPermissions();
        if ($permissions->isEmpty()) {
            return false;
        }

        $sectorPerms = $permissions->where('sector_id', (string) $sectorId);
        if ($sectorPerms->isEmpty()) {
            return false;
        }

        // Broad sector-level access granted (sub_sector_id is NULL)
        if ($sectorPerms->whereNull('sub_sector_id')->isNotEmpty()) {
            return true;
        }

        if ($subSectorId !== null) {
            return $sectorPerms->where('sub_sector_id', (string) $subSectorId)->isNotEmpty();
        }

        return true;
    }

    /**
     * Get the list of sectors the current user may access.
     * Returns empty collection if user has no assigned permissions.
     */
    protected function getAllowedSectors()
    {
        $sectorPermissions = $this->getUserSectorPermissions();

        if ($sectorPermissions->isEmpty()) {
            return collect();
        }

        $allowedSectorIds = $sectorPermissions->pluck('sector_id')->unique()->toArray();
        return TblRefSector::whereIn('sector_id', $allowedSectorIds)->orderBy('sort_order')->get();
    }

    /**
     * Get the allowed sub-sectors for the currently selected sector.
     * Filters by the user's permissions.
     */
    protected function getAllowedSubSectors($sectorId)
    {
        if (empty($sectorId)) {
            return collect();
        }

        $sectorPermissions = $this->getUserSectorPermissions();

        if ($sectorPermissions->isEmpty()) {
            return collect();
        }

        // Check if user has broad "all sub-sectors" access for this sector
        $hasAllAccess = $sectorPermissions
            ->where('sector_id', (string) $sectorId)
            ->whereNull('sub_sector_id')
            ->isNotEmpty();

        if ($hasAllAccess) {
            return TblRefSubSector::where('sector_id', $sectorId)->orderBy('sort_order')->get();
        }

        // Return only the specific sub-sectors the user has access to for this sector
        $allowedSubSectorIds = $sectorPermissions
            ->where('sector_id', (string) $sectorId)
            ->whereNotNull('sub_sector_id')
            ->pluck('sub_sector_id')
            ->toArray();

        return TblRefSubSector::whereIn('sub_sector_id', $allowedSubSectorIds)->orderBy('sort_order')->get();
    }

    public function render()
    {
        $sectorPermissions = $this->getUserSectorPermissions();
        $allowedSectors    = $this->getAllowedSectors();

        // Build the base query with joins for sector/sub-sector names
        $query = TblRefDocumentTypes::search($this->search);

        if ($sectorPermissions->isEmpty()) {
            // No permissions assigned -> No data shown
            $query->whereRaw('0 = 1');
        } elseif (empty($this->filterSector)) {
            // No sector chosen in table filter -> No data shown until sector is selected
            $query->whereRaw('0 = 1');
        } else {
            // Only selected sector / subsector ang makita
            $query->where('tbl_ref_document_types.sector', $this->filterSector);

            if (!empty($this->filterSubSector)) {
                $query->where('tbl_ref_document_types.sub_sector', $this->filterSubSector);
            } else {
                $allowedSubIds = $this->getAllowedSubSectors($this->filterSector)->pluck('sub_sector_id')->toArray();
                $query->whereIn('tbl_ref_document_types.sub_sector', $allowedSubIds);
            }
        }

        $data = $query->paginate(10);

        // Sector and sub-sector dropdowns are filtered by the user's permissions
        $subSectors         = $this->getAllowedSubSectors($this->selectedSector);
        $filterSubSectors   = $this->getAllowedSubSectors($this->filterSector);

        return view('livewire.document-type-entry', compact('subSectors', 'filterSubSectors', 'allowedSectors'), [
            'data'                   => $data,
            'currentPage'            => $data->currentPage(),
            'totalPages'             => $data->lastPage(),
            'totalRecords'           => $data->total(),
            'noRecords'              => $data->isEmpty(),
            'hasNoSectorPermissions' => $sectorPermissions->isEmpty(),
            'noFilterSectorSelected' => empty($this->filterSector),
        ]);
    }

    // Resets the page upon searching or filtering. This way, if you are in page 3 and the record you are searching for is on page 1, this function will reset the page and make those record from page 1 searchable.
    public function updating()
    {
        $this->resetPage();
    }
}

