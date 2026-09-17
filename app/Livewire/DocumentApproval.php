<?php

namespace App\Livewire;

use App\Models\TblDocumentInformation;
use App\Models\TblDocumentData;
use App\Models\TblRefSector;
use App\Models\TblRefSubSector;
use App\Models\TblUserAccessPermission;
use App\Models\TblUserSectorPermission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentApproval extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending'; // 'pending', 'approved', 'declined', 'all'
    public $sectorFilter = '';
    public $subSectorFilter = '';

    // Modal states
    public $isReviewModalOpen = false;
    public $isDeclineModalOpen = false;
    public $selectedDocData = [];
    public $attachedFiles = [];

    // Decline form state
    public $declineTransId = '';
    public $declineReason = '';

    // Reset pagination when searching or changing filters
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSectorFilter()
    {
        $this->subSectorFilter = '';
        $this->resetPage();
    }

    public function updatingSubSectorFilter()
    {
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────
    // SECTOR ACCESS PERMISSION HELPERS
    // ─────────────────────────────────────────────────────────

    /**
     * Returns the sector permissions for the currently logged-in user.
     * Returns an empty collection if no permissions assigned (no access).
     */
    protected function getUserSectorPermissions()
    {
        $userId = Auth::id();
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
     * Get sectors the current user is allowed to access.
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
     * Get allowed sub-sectors for the selected sector, filtered by user permissions.
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

        $hasAllAccess = $sectorPermissions
            ->where('sector_id', (string) $sectorId)
            ->whereNull('sub_sector_id')
            ->isNotEmpty();

        if ($hasAllAccess) {
            return TblRefSubSector::where('sector_id', $sectorId)->orderBy('sort_order')->get();
        }

        $allowedSubSectorIds = $sectorPermissions
            ->where('sector_id', (string) $sectorId)
            ->whereNotNull('sub_sector_id')
            ->pluck('sub_sector_id')
            ->toArray();

        return TblRefSubSector::whereIn('sub_sector_id', $allowedSubSectorIds)->orderBy('sort_order')->get();
    }

    // ─────────────────────────────────────────────────────────
    // REVIEW MODAL
    // ─────────────────────────────────────────────────────────

    public function reviewDocument($trans_id)
    {
        $doc = TblDocumentInformation::with([
            'tbl_ref_sector',
            'tbl_ref_sub_sector',
            'tbl_ref_document_types'
        ])->findOrFail($trans_id);

        if (!$this->hasSectorAccess($doc->sector, $doc->sub_sector)) {
            abort(403, 'Unauthorized access to this document.');
        }

        $this->selectedDocData = [
            'trans_id'        => $doc->trans_id,
            'document_desc'   => $doc->document_desc,
            'sector_name'     => $doc->tbl_ref_sector->sector_name ?? $doc->sector,
            'sub_sector_name' => $doc->tbl_ref_sub_sector->sub_sector_name ?? $doc->sub_sector,
            'doctype_desc'    => $doc->tbl_ref_document_types->doctype_desc ?? $doc->document_type,
            'document_source' => $doc->document_source,
            'create_user'     => $doc->create_user,
            'created_at'      => $doc->created_at ? $doc->created_at->format('F d, Y \a\t h:i A') : 'N/A',
            'approval_status' => $doc->approval_status,
            'approved_by'     => $doc->approved_by,
            'approved_at'     => $doc->approved_at ? \Carbon\Carbon::parse($doc->approved_at)->format('F d, Y \a\t h:i A') : null,
            'decline_reason'  => $doc->decline_reason,
        ];

        $this->attachedFiles = TblDocumentData::select('document_id', 'document_desc', 'file_name', 'file_size', 'file_type', 'trans_id')
            ->where('document_id', $trans_id)
            ->where('is_active', 1)
            ->get()
            ->toArray();

        $this->isReviewModalOpen = true;
    }

    public function closeReviewModal()
    {
        $this->isReviewModalOpen = false;
        $this->selectedDocData = [];
        $this->attachedFiles = [];
    }

    // ─────────────────────────────────────────────────────────
    // APPROVE ACTION
    // ─────────────────────────────────────────────────────────

    public function approve($trans_id)
    {
        $doc = TblDocumentInformation::findOrFail($trans_id);

        if (!$this->hasSectorAccess($doc->sector, $doc->sub_sector)) {
            abort(403, 'Unauthorized action for this document.');
        }

        $doc->update([
            'approval_status' => 'approved',
            'approved_by'     => Auth::user()->name,
            'approved_at'     => now(),
            'decline_reason'  => null,
        ]);

        if ($this->isReviewModalOpen) {
            $this->closeReviewModal();
        }

        session()->flash('status', 'Document "' . $doc->document_desc . '" has been APPROVED successfully.');
    }

    // ─────────────────────────────────────────────────────────
    // DECLINE ACTION
    // ─────────────────────────────────────────────────────────

    public function openDeclineModal($trans_id)
    {
        $this->declineTransId = $trans_id;
        $this->declineReason = '';
        $this->resetValidation();
        $this->isDeclineModalOpen = true;
    }

    public function closeDeclineModal()
    {
        $this->isDeclineModalOpen = false;
        $this->declineTransId = '';
        $this->declineReason = '';
        $this->resetValidation();
    }

    public function confirmDecline()
    {
        $this->validate([
            'declineReason' => 'required|min:3|max:500',
        ], [
            'declineReason.required' => 'Please provide a reason for declining this document.',
            'declineReason.min'      => 'The decline reason must be at least 3 characters.',
            'declineReason.max'      => 'The decline reason must not exceed 500 characters.',
        ]);

        $doc = TblDocumentInformation::findOrFail($this->declineTransId);

        if (!$this->hasSectorAccess($doc->sector, $doc->sub_sector)) {
            abort(403, 'Unauthorized action for this document.');
        }

        $doc->update([
            'approval_status' => 'declined',
            'approved_by'     => Auth::user()->name,
            'approved_at'     => now(),
            'decline_reason'  => $this->declineReason,
        ]);

        $this->closeDeclineModal();

        if ($this->isReviewModalOpen) {
            $this->closeReviewModal();
        }

        session()->flash('status', 'Document "' . $doc->document_desc . '" has been DECLINED.');
    }

    // ─────────────────────────────────────────────────────────
    // RENDER
    // ─────────────────────────────────────────────────────────

    public function render()
    {
        // Check CMS menu permissions for Menu ID 7
        $permission = TblUserAccessPermission::where('id_user', Auth::id())
            ->where('id_cms_menu', 7)
            ->first();

        $sectorPermissions = $this->getUserSectorPermissions();

        // Base query with joins
        $query = TblDocumentInformation::search($this->search);

        if ($sectorPermissions->isEmpty()) {
            $query->whereRaw('0 = 1');
            $pendingCount  = 0;
            $approvedCount = 0;
            $declinedCount = 0;
            $totalCount    = 0;
            $sectors       = collect();
            $subSectors    = collect();
        } else {
            // Restrict by user sector/sub-sector permissions
            $query->where(function ($q) use ($sectorPermissions) {
                foreach ($sectorPermissions as $perm) {
                    $q->orWhere(function ($inner) use ($perm) {
                        $inner->where('tbl_document_information.sector', $perm->sector_id);
                        if ($perm->sub_sector_id !== null) {
                            $inner->where('tbl_document_information.sub_sector', $perm->sub_sector_id);
                        }
                    });
                }
            });

            // Status filter
            if ($this->statusFilter !== 'all') {
                $query->where('tbl_document_information.approval_status', $this->statusFilter);
            }

            // Sector filter
            if (!empty($this->sectorFilter)) {
                $query->where('tbl_document_information.sector', $this->sectorFilter);
            }

            // Sub-Sector filter
            if (!empty($this->subSectorFilter)) {
                $query->where('tbl_document_information.sub_sector', $this->subSectorFilter);
            }

            // Base query for counts matching sector permissions
            $baseCountQuery = TblDocumentInformation::query()->where(function ($q) use ($sectorPermissions) {
                foreach ($sectorPermissions as $perm) {
                    $q->orWhere(function ($inner) use ($perm) {
                        $inner->where('tbl_document_information.sector', $perm->sector_id);
                        if ($perm->sub_sector_id !== null) {
                            $inner->where('tbl_document_information.sub_sector', $perm->sub_sector_id);
                        }
                    });
                }
            });

            if (!empty($this->sectorFilter)) {
                $baseCountQuery->where('tbl_document_information.sector', $this->sectorFilter);
            }

            if (!empty($this->subSectorFilter)) {
                $baseCountQuery->where('tbl_document_information.sub_sector', $this->subSectorFilter);
            }

            $pendingCount  = (clone $baseCountQuery)->where('approval_status', 'pending')->count();
            $approvedCount = (clone $baseCountQuery)->where('approval_status', 'approved')->count();
            $declinedCount = (clone $baseCountQuery)->where('approval_status', 'declined')->count();
            $totalCount    = (clone $baseCountQuery)->count();

            $sectors    = $this->getAllowedSectors();
            $subSectors = $this->getAllowedSubSectors($this->sectorFilter);
        }

        $data = $query->orderBy('tbl_document_information.created_at', 'desc')->paginate(10);

        return view('livewire.document-approval', [
            'data'                   => $data,
            'currentPage'            => $data->currentPage(),
            'totalPages'             => $data->lastPage(),
            'totalRecords'           => $data->total(),
            'noRecords'              => $data->isEmpty(),
            'pendingCount'           => $pendingCount,
            'approvedCount'          => $approvedCount,
            'declinedCount'          => $declinedCount,
            'totalCount'             => $totalCount,
            'sectors'                => $sectors,
            'subSectors'             => $subSectors,
            'permission'             => $permission,
            'hasNoSectorPermissions' => $sectorPermissions->isEmpty(),
        ]);
    }
}
