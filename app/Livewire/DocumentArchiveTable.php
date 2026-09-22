<?php

namespace App\Livewire;

use App\Models\TblDocumentInformation;
use App\Models\TblDocumentData;
use App\Models\TblRefDepartment;
use App\Models\TblRefSector;
use App\Models\TblRefSubSector;
use App\Models\TblUserSectorPermission;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str; //THIS IS FOR THE str::random()
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Storage;
use Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DocumentArchiveTable extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    public $isModalOpen = 0;

    public $isUploadFieldOpen = 0; // This modal is for when clicking upload button. When user wants to upload another file.

    public $alertPDFdelete = 0; // This is for the confirmation message if user wants to delete a PDF.
    public $fileToDeleteId;

    // Document-level delete (Administrator only)
    public $alertDocumentDelete = false;
    public $documentToDeleteId;

    public $isEditFileModalOpen = false; // Modal for editing/updating an existing uploaded file
    public $editFileTransId;
    public $editFileDocDesc;
    public $editFileName;
    public $editFileSize;
    public $editNewPdfFile;

    public $Posttransid; // For edit

    public $pdfFileInfo = []; // this property is for storing array of pdf file info

    public $pdfFileDownload;

    // Input Fields
    // DYNAMIC SELECT
    public $selectedSector;
    public $selectedSubSector;
    public $selectedDocumentType;
    // END DYNAMIC SELECT
    public $document_source;
    public $document_desc;
    public $document_desc2;
    public $trans_id;

    public $Newdocument_desc2;
    public $NewpdfFile;
    // End Input Fields

    public $currentApprovalStatus;
    public $currentDeclineReason;

    // Table Filter Properties (for Admin and general filtering)
    public $filterSector = '';
    public $filterSubSector = '';
    public $filterYear = '';
    public $filterSemester = '';

    public $listeners = ['deletePDF'];

    public $pdfFile;

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

    public function updatedFilterYear()
    {
        $this->resetPage();
    }

    public function updatedFilterSemester()
    {
        $this->resetPage();
    }

    public function mount()
    {
        // Pre-populate source from the authenticated user's office (department ID)
        if (auth()->check() && !empty(auth()->user()->office)) {
            $this->document_source = auth()->user()->office;
        }
    }

    // MODAL ------------------------------------------------
    public function add()
    {
        $this->reset('selectedSector', 'selectedSubSector', 'selectedDocumentType', 'document_desc', 'document_desc2', 'Posttransid', 'currentApprovalStatus', 'currentDeclineReason', 'isUploadFieldOpen', 'isEditFileModalOpen', 'alertPDFdelete');

        // Auto-populate Source from the authenticated user's office — always
        $this->document_source = auth()->user()->office ?? '';

        // Auto-populate from active table filter if already selected
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
        $this->closeEditFileModal();
        $this->hideUploadInputField();
        $this->closealertDelete();
    }

    public function addfile()
    {
        $this->showUploadInputField();
    }
    public function showUploadInputField()
    {
        $this->isUploadFieldOpen = true;
    }
    public function hideUploadInputField()
    {
        $this->isUploadFieldOpen = false;
        $this->reset('Newdocument_desc2', 'NewpdfFile');
        $this->resetValidation();
    }

    public function openalertDelete($transId = null)
    {
        $this->fileToDeleteId = $transId;
        $this->alertPDFdelete = true;
    }
    public function closealertDelete()
    {
        $this->alertPDFdelete = false;
        $this->fileToDeleteId = null;
    }

    public function editUploadedFile($transId)
    {
        $file = TblDocumentData::where('trans_id', $transId)->where('is_active', 1)->first();
        if ($file) {
            $this->editFileTransId = $file->trans_id;
            $this->editFileDocDesc = $file->document_desc;
            $this->editFileName = $file->file_name;
            $this->editFileSize = $file->file_size;
            $this->editNewPdfFile = null;
            $this->resetValidation();
            $this->isEditFileModalOpen = true;
        }
    }

    public function closeEditFileModal()
    {
        $this->isEditFileModalOpen = false;
        $this->editFileTransId = null;
        $this->editFileDocDesc = null;
        $this->editFileName = null;
        $this->editFileSize = null;
        $this->editNewPdfFile = null;
        $this->resetValidation();
    }
    // END MODAL --------------------------------------------

    // STORE ------------------------------------------------
    public function store()
    {
        // VALIDATION
        $rules = [
            'selectedSector'        => 'required',
            'selectedSubSector'     => 'required',
            'selectedDocumentType'  =>  'required',
            'document_desc'         =>  'required',
            'document_source'       => 'required',

            'document_desc2'        => 'required',
            'pdfFile'               => 'required|mimes:pdf',
        ];

        $messages = [
            'selectedSector.required'       => 'Sector is required.',
            'selectedSubSector.required'    => 'Sub Sector is required.',
            'document_desc.required'        => 'Description is required.',
            'document_source.required'      => 'Source is required.',

            'document_desc2.required'       => 'Document Description is required.',
            'pdfFile.required'              => 'File is required.',
            'pdfFile.mimes'                 => 'The file must be a PDF.',
        ];

        $this->validate($rules, $messages);
        // END

        // Security: Ensure user has permission for the selected sector and sub-sector
        if (!$this->hasSectorAccess($this->selectedSector, $this->selectedSubSector)) {
            $this->addError('selectedSector', 'You are not authorized to upload documents for this sector or sub-sector.');
            return;
        }

        // Generate random letters and numbers for trans_id
        $timestamp = now()->timestamp;
        $randomString = Str::random(32);
        $randomString2 = Str::random(40);
        $transID = $timestamp . $randomString;
        $transID2 = $randomString2 . $timestamp;

        TblDocumentInformation::create([
            'trans_id'          =>      $transID,
            'sector'            =>      $this->selectedSector,
            'sub_sector'        =>      $this->selectedSubSector,
            'document_type'     =>      $this->selectedDocumentType,
            'document_desc'     =>      $this->document_desc,
            'document_source'   =>      $this->document_source,
            'approval_status'   =>      'pending',
            'create_user'       =>      Auth::user()->name,
        ]);

        // PDF IS UPLOADED DIRECLY TO DATABASE
        if ($this->pdfFile) {
            $pdfFileContent = file_get_contents($this->pdfFile->path());
            TblDocumentData::create([
                'trans_id'         =>       $transID2,
                'document_id'      =>       $transID,
                'document_desc'    =>       $this->document_desc2,
                'document_data'    =>       $pdfFileContent, // Save as blob file.
                'file_name'        =>       $this->pdfFile->getClientOriginalName(),
                'file_size'        =>       $this->pdfFile->getSize(),
                'file_type'        =>       $this->pdfFile->extension(),
                'upload_user'      =>       Auth::user()->name,
            ]);
        }

        session()->flash('status', 'Added successfully.');

        return redirect()->to('/archive');
    }
    // END STORE --------------------------------------------

    // STORE NEW PDF based on the document_id
    public function addNewPdf()
    {
        $rules = [
            'Newdocument_desc2'        => 'required',
            'NewpdfFile'               => 'required|mimes:pdf',
            'Posttransid'              => 'required',
        ];

        $messages = [
            'Newdocument_desc2.required'       =>       'Document Description is required.',
            'NewpdfFile.required'              =>       'File is required.',
            'NewpdfFile.mimes'                 =>       'The file must be a PDF.',
        ];

        $this->validate($rules, $messages);

        if ($this->Posttransid) {
            $parentDoc = TblDocumentInformation::findOrFail($this->Posttransid);
            if ($this->isAdministrator()) {
                if (!$this->hasSectorAccess($parentDoc->sector, $parentDoc->sub_sector)) {
                    abort(403, 'Unauthorized action for this sector.');
                }
            } elseif (!$this->isDocumentOwner($parentDoc)) {
                abort(403, 'Unauthorized action.');
            }
        }

        $timestamp = now()->timestamp;
        $randomString2 = Str::random(40);
        $transID2 = $randomString2 . $timestamp;

        // PDF IS UPLOADED DIRECLY TO DATABASE
        if ($this->NewpdfFile) {
            $NewpdfFileContent = file_get_contents($this->NewpdfFile->path());
            TblDocumentData::create([
                'trans_id'         =>       $transID2,
                'document_id'      =>       $this->Posttransid,
                'document_desc'    =>       $this->Newdocument_desc2,
                'document_data'    =>       $NewpdfFileContent, // Save as blob file.
                'file_name'        =>       $this->NewpdfFile->getClientOriginalName(),
                'file_size'        =>       $this->NewpdfFile->getSize(),
                'file_type'        =>       $this->NewpdfFile->extension(),
                'upload_user'      =>       Auth::user()->name,
            ]);
        }

        $this->hideUploadInputField();
        $this->refreshPdfFileInfo();
        session()->flash('file_status', 'New document file uploaded successfully.');
    }
    // END STORE NEW PDF

    // UPDATE EXISTING UPLOADED FILE
    public function updateUploadedFile()
    {
        $rules = [
            'editFileDocDesc' => 'required',
            'editFileTransId' => 'required',
        ];

        $messages = [
            'editFileDocDesc.required' => 'Document Description is required.',
        ];

        if ($this->editNewPdfFile) {
            $rules['editNewPdfFile'] = 'mimes:pdf';
            $messages['editNewPdfFile.mimes'] = 'The replacement file must be a PDF.';
        }

        $this->validate($rules, $messages);

        $file = TblDocumentData::where('trans_id', $this->editFileTransId)->first();
        if ($file) {
            $parentDoc = TblDocumentInformation::where('trans_id', $file->document_id)->first();
            if ($parentDoc && !$this->isAdministrator() && !$this->isDocumentOwner($parentDoc)) {
                abort(403, 'Unauthorized action.');
            }

            $updateData = [
                'document_desc' => $this->editFileDocDesc,
            ];

            if ($this->editNewPdfFile) {
                $pdfContent = file_get_contents($this->editNewPdfFile->path());
                $updateData['document_data'] = $pdfContent;
                $updateData['file_name']     = $this->editNewPdfFile->getClientOriginalName();
                $updateData['file_size']     = $this->editNewPdfFile->getSize();
                $updateData['file_type']     = $this->editNewPdfFile->extension();
                $updateData['upload_user']   = Auth::user()->name;
            }

            $file->update($updateData);

            $this->refreshPdfFileInfo();
            $this->closeEditFileModal();
            session()->flash('file_status', 'Uploaded file updated successfully.');
        }
    }
    // END UPDATE EXISTING UPLOADED FILE

    public function refreshPdfFileInfo()
    {
        if ($this->Posttransid) {
            $files = TblDocumentData::select('document_id', 'document_desc', 'file_name', 'file_size', 'file_type', 'trans_id')
                ->where('document_id', $this->Posttransid)
                ->where('is_active', '1')
                ->get();

            $this->pdfFileInfo = $files->toArray();
        }
    }

    // RETRIEVE inside edit modal
    public function edit($trans_id)
    {
        $item = TblDocumentInformation::findOrFail($trans_id);

        if ($this->isAdministrator()) {
            if (!$this->hasSectorAccess($item->sector, $item->sub_sector)) {
                abort(403, 'Unauthorized access to this document.');
            }
        } elseif (!$this->isDocumentOwner($item)) {
            abort(403, 'Unauthorized access to this document.');
        }

        $this->Posttransid = $trans_id;
        $this->selectedSector = $item->sector;
        $this->selectedSubSector = $item->sub_sector;
        $this->selectedDocumentType = $item->document_type;
        $this->document_desc = $item->document_desc;
        $this->document_source = $item->document_source;
        $this->currentApprovalStatus = $item->approval_status;
        $this->currentDeclineReason = $item->decline_reason;

        // Retrieve information from TblDocumentData (uploaded files and additional columns)
        $files = TblDocumentData::select('document_id', 'document_desc', 'file_name', 'file_size', 'file_type', 'trans_id')
            ->where('document_id', $trans_id)
            ->where('is_active', '1')
            ->get();

        // Store information in the $pdfFileInfo array
        $this->pdfFileInfo = $files->toArray();

        $this->resetValidation();
        $this->openModal();
    }
    // END

    // UPDATE
    public function update()
    {
        // VALIDATION
        $rules = [
            'selectedSector'        => 'required',
            'selectedSubSector'     => 'required',
            'selectedDocumentType'  =>  'required',
            'document_desc'         =>  'required',
            'document_source'       => 'required',
        ];

        $messages = [
            'selectedSector.required'       => 'Sector is required.',
            'selectedSubSector.required'    => 'Sub Sector is required.',
            'document_desc.required'        => 'Description is required.',
            'document_source.required'      => 'Source is required.',
        ];

        $this->validate($rules, $messages);
        // END

        // Security check: ensure user has permission for the selected sector and sub-sector
        if (!$this->hasSectorAccess($this->selectedSector, $this->selectedSubSector)) {
            $this->addError('selectedSector', 'You are not authorized to assign this sector or sub-sector.');
            return;
        }

        if ($this->Posttransid) {
            $item = TblDocumentInformation::findOrFail($this->Posttransid);

            if ($this->isAdministrator()) {
                if (!$this->hasSectorAccess($item->sector, $item->sub_sector)) {
                    abort(403, 'Unauthorized access to this document.');
                }
            } elseif (!$this->isDocumentOwner($item)) {
                abort(403, 'Unauthorized access to this document.');
            }

            // If the document was previously declined, revising it resets it back to pending for administrator review
            $wasDeclined = ($item->approval_status === 'declined');
            $newStatus = $wasDeclined ? 'pending' : $item->approval_status;

            $item->update([
                'sector'            =>      $this->selectedSector,
                'sub_sector'        =>      $this->selectedSubSector,
                'document_type'     =>      $this->selectedDocumentType,
                'document_desc'     =>      $this->document_desc,
                'document_source'   =>      $this->document_source,
                'approval_status'   =>      $newStatus,
                'approved_by'       =>      $wasDeclined ? null : $item->approved_by,
                'approved_at'       =>      $wasDeclined ? null : $item->approved_at,
                'decline_reason'    =>      $wasDeclined ? null : $item->decline_reason,
            ]);

            $msg = $wasDeclined
                ? 'Document revised and resubmitted for approval successfully.'
                : 'Updated successfully.';

            session()->flash('status', $msg);
            $this->closeModal();
            $this->reset('selectedSector', 'selectedSubSector', 'selectedDocumentType', 'document_desc', 'document_source', 'Posttransid', 'alertPDFdelete', 'currentApprovalStatus', 'currentDeclineReason');
        }
    }
    // END UPDATE

    // Preview embedded PDF File
    public function previewPdf($trans_id)
    {
        $dataPdf = TblDocumentData::where('trans_id', $trans_id)->first();

        if (!$dataPdf) {
            // Handle case where no data is found for the given trans_id
            return response()->view('pdf-preview', ['file_contents' => null, 'title' => 'Document Not Found']);
        }

        // Restrict regular users so they can only preview files from their own submitted documents
        if (!$this->isAdministrator()) {
            $parentDoc = TblDocumentInformation::where('trans_id', $dataPdf->document_id)->first();
            if ($parentDoc && !$this->isDocumentOwner($parentDoc)) {
                abort(403, 'Unauthorized access to this document preview.');
            }
        }

        // Fetch the title from the database
        $title = $dataPdf->file_name;

        // In the database, the document_data is in blob format. We encoded it as base64 and in the pdf-review, it can handle base64.
        $file_contents = base64_encode($dataPdf->document_data);

        return view('pdf-preview', compact('file_contents', 'title'));
    }
    // End preview PDF

    // DOWNLOAD FILE
    public function download($transId)
    {
        $dataPdf = TblDocumentData::where('trans_id', $transId)->first();

        if (!$dataPdf) {
            abort(404, 'File not found.');
        }

        // Restrict regular users so they can only download files from their own submitted documents
        if (!$this->isAdministrator()) {
            $parentDoc = TblDocumentInformation::where('trans_id', $dataPdf->document_id)->first();
            if ($parentDoc && !$this->isDocumentOwner($parentDoc)) {
                abort(403, 'Unauthorized access to download this document.');
            }
        }

        $pdf_contents = $dataPdf->document_data;

        return response($pdf_contents)
            ->header('Cache-Control', 'no-cache private')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Type', $dataPdf->file_type)
            ->header('Content-length', strlen($pdf_contents))
            ->header('Content-Disposition', 'attachment; filename=' . $dataPdf->file_name)
            ->header('Content-Transfer-Encoding', 'binary');
    }
    // END DOWNLOAD FILE

    // SOFT DELETE: is_active will be updated to 0 for deleted files.
    public function deletePDF($transId = null)
    {
        $targetId = $transId ?? $this->fileToDeleteId;
        if ($targetId) {
            $file = TblDocumentData::where('trans_id', $targetId)->first();
            if ($file) {
                $parentDoc = TblDocumentInformation::where('trans_id', $file->document_id)->first();
                if ($parentDoc && !$this->isAdministrator() && !$this->isDocumentOwner($parentDoc)) {
                    abort(403, 'Unauthorized action.');
                }

                $file->update([
                    'is_active' => 0,
                ]);

                $this->refreshPdfFileInfo();
                $this->closealertDelete();
                session()->flash('file_status', 'File deleted successfully.');
            }
        }
    }
    // END SOFT DELETE

    // ─────────────────────────────────────────────────────────
    // DELETE ENTIRE DOCUMENT (Administrator only)
    // ─────────────────────────────────────────────────────────

    /**
     * Open the confirmation modal for deleting an entire document archive entry.
     * Only Administrators can trigger this.
     */
    public function openDeleteDocumentConfirm(string $transId): void
    {
        if (!$this->isAdministrator()) {
            abort(403, 'Unauthorized action.');
        }

        $this->documentToDeleteId = $transId;
        $this->alertDocumentDelete = true;
    }

    /**
     * Close the document delete confirmation modal without taking action.
     */
    public function closeDeleteDocumentConfirm(): void
    {
        $this->alertDocumentDelete = false;
        $this->documentToDeleteId = null;
    }

    /**
     * Soft-delete the document archive entry and all its associated files.
     * Administrator only.
     */
    public function deleteDocument(): void
    {
        if (!$this->isAdministrator()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$this->documentToDeleteId) {
            return;
        }

        $document = TblDocumentInformation::where('trans_id', $this->documentToDeleteId)->first();

        if (!$document) {
            $this->closeDeleteDocumentConfirm();
            return;
        }

        // Soft-delete all associated files
        TblDocumentData::where('document_id', $this->documentToDeleteId)
            ->update(['is_active' => 0]);

        // Soft-delete the document record itself
        $document->update(['is_active' => 0]);

        $this->closeDeleteDocumentConfirm();
        session()->flash('status', 'Document deleted successfully.');
    }
    // END DELETE ENTIRE DOCUMENT

    public function updating()
    {
        $this->resetPage();
    }

    // ─────────────────────────────────────────────────────────
    // AUTHORIZATION & PERMISSION HELPERS
    // ─────────────────────────────────────────────────────────

    /**
     * Check if the currently authenticated user is an administrator.
     */
    public function isAdministrator(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return (int) $user->user_role_id === 1
            || strtolower(optional($user->role)->user_role_name ?? '') === 'administrator'
            || strtolower(optional($user->role)->user_role_name ?? '') === 'admin';
    }

    /**
     * Check if the document belongs to the currently authenticated user.
     */
    public function isDocumentOwner(TblDocumentInformation $document): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $document->create_user === $user->name || $document->create_user === $user->user_id;
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
     * Get sectors the current user is allowed to access.
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

        // Check if user has broad "all sub-sectors" access for this sector
        $hasAllAccess = $sectorPermissions
            ->where('sector_id', (string) $sectorId)
            ->whereNull('sub_sector_id')
            ->isNotEmpty();

        if ($hasAllAccess) {
            return TblRefSubSector::where('sector_id', $sectorId)->orderBy('sort_order')->get();
        }

        // Return only the specific sub-sectors granted to this user
        $allowedSubSectorIds = $sectorPermissions
            ->where('sector_id', (string) $sectorId)
            ->whereNotNull('sub_sector_id')
            ->pluck('sub_sector_id')
            ->toArray();

        return TblRefSubSector::whereIn('sub_sector_id', $allowedSubSectorIds)->orderBy('sort_order')->get();
    }

    /**
     * Get available submission years dynamically from document database and surrounding years.
     */
    public function getAvailableYears(): array
    {
        $currentYear = (int) Carbon::now()->year;

        $docYears = TblDocumentInformation::select('created_at', 'create_date')
            ->get()
            ->map(function ($doc) {
                if ($doc->created_at) {
                    return (int) $doc->created_at->format('Y');
                }
                if (!empty($doc->create_date)) {
                    $parts = explode(' ', $doc->create_date);
                    $dateParts = explode('/', $parts[0]);
                    if (isset($dateParts[2]) && is_numeric($dateParts[2])) {
                        return (int) $dateParts[2];
                    }
                }
                return null;
            })
            ->filter()
            ->unique()
            ->toArray();

        $defaultRange = range(max($currentYear, 2026), 2017);
        $allYears = array_unique(array_merge($docYears, $defaultRange));
        rsort($allYears);

        return array_values($allYears);
    }

    /**
     * Apply year and semester date filtering to a document query.
     */
    protected function applyDateRangeToQuery($query, string $dateColumn = 'tbl_document_information.created_at', ?string $legacyDateColumn = 'tbl_document_information.create_date')
    {
        if (!empty($this->filterYear)) {
            $year = (int) $this->filterYear;
            if ($this->filterSemester === '1') {
                $from = Carbon::createFromDate($year, 1, 1)->startOfDay()->format('Y-m-d H:i:s');
                $to   = Carbon::createFromDate($year, 6, 30)->endOfDay()->format('Y-m-d H:i:s');
            } elseif ($this->filterSemester === '2') {
                $from = Carbon::createFromDate($year, 7, 1)->startOfDay()->format('Y-m-d H:i:s');
                $to   = Carbon::createFromDate($year, 12, 31)->endOfDay()->format('Y-m-d H:i:s');
            } else {
                $from = Carbon::createFromDate($year, 1, 1)->startOfDay()->format('Y-m-d H:i:s');
                $to   = Carbon::createFromDate($year, 12, 31)->endOfDay()->format('Y-m-d H:i:s');
            }

            $query->where(function ($q) use ($from, $to, $dateColumn, $legacyDateColumn) {
                $q->whereBetween($dateColumn, [$from, $to]);
                if ($legacyDateColumn) {
                    $q->orWhereRaw("STR_TO_DATE({$legacyDateColumn}, '%c/%e/%Y %r') BETWEEN ? AND ?", [$from, $to]);
                }
            });
        } elseif (!empty($this->filterSemester)) {
            if ($this->filterSemester === '1') {
                $query->where(function ($q) use ($dateColumn, $legacyDateColumn) {
                    $q->whereRaw("MONTH({$dateColumn}) BETWEEN 1 AND 6");
                    if ($legacyDateColumn) {
                        $q->orWhereRaw("MONTH(STR_TO_DATE({$legacyDateColumn}, '%c/%e/%Y %r')) BETWEEN 1 AND 6");
                    }
                });
            } elseif ($this->filterSemester === '2') {
                $query->where(function ($q) use ($dateColumn, $legacyDateColumn) {
                    $q->whereRaw("MONTH({$dateColumn}) BETWEEN 7 AND 12");
                    if ($legacyDateColumn) {
                        $q->orWhereRaw("MONTH(STR_TO_DATE({$legacyDateColumn}, '%c/%e/%Y %r')) BETWEEN 7 AND 12");
                    }
                });
            }
        }
    }

    public function render()
    {
        $sectorPermissions = $this->getUserSectorPermissions();
        $allowedSectors    = $this->getAllowedSectors();

        // Build the base query with joins
        $query = TblDocumentInformation::search($this->search);

        // Exclude soft-deleted documents
        $query->where('tbl_document_information.is_active', '!=', '0');

        // Apply date range filter (Year / Semester)
        $this->applyDateRangeToQuery($query);

        if ($this->isAdministrator()) {
            // ADMIN: Enforce sector/subsector visibility
            if ($sectorPermissions->isEmpty()) {
                // No chosen sector/subsector assigned -> No data upload (0 records shown)
                $query->whereRaw('0 = 1');
            } elseif (empty($this->filterSector)) {
                // No sector chosen in filter → show all documents from all allowed sectors
                $allowedSectorIds = $allowedSectors->pluck('sector_id')->toArray();
                $query->whereIn('tbl_document_information.sector', $allowedSectorIds);

                // Show approved docs from allowed sectors, plus the admin's own submissions
                $query->where(function ($q) {
                    $q->where('tbl_document_information.approval_status', 'approved')
                      ->orWhere('tbl_document_information.create_user', Auth::user()->name)
                      ->orWhere('tbl_document_information.create_user', Auth::user()->user_id);
                });
            } else {
                // Only selected sector / subsector ang makita
                $query->where('tbl_document_information.sector', $this->filterSector);

                if (!empty($this->filterSubSector)) {
                    $query->where('tbl_document_information.sub_sector', $this->filterSubSector);
                } else {
                    // Filter to all sub-sectors allowed for this admin under the selected sector
                    $allowedSubIds = $this->getAllowedSubSectors($this->filterSector)->pluck('sub_sector_id')->toArray();
                    $query->whereIn('tbl_document_information.sub_sector', $allowedSubIds);
                }

                // Approval status filtering: show approved documents from allowed sectors
                // PLUS show admin's own submissions (approved, pending, or declined for revision)
                $query->where(function ($q) {
                    $q->where('tbl_document_information.approval_status', 'approved')
                      ->orWhere('tbl_document_information.create_user', Auth::user()->name)
                      ->orWhere('tbl_document_information.create_user', Auth::user()->user_id);
                });
            }
        } else {
            // Regular user: can only view what they submitted, within their allowed sectors
            if ($sectorPermissions->isEmpty()) {
                $query->whereRaw('0 = 1');
            } else {
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

                $query->where(function ($q) {
                    $q->where('tbl_document_information.create_user', Auth::user()->name)
                      ->orWhere('tbl_document_information.create_user', Auth::user()->user_id);
                });
            }
        }

        // PAGINATION
        $data = $query->orderBy('tbl_document_information.created_at', 'desc')->paginate(10);
        // END

        // Dynamic Select Values for Add/Edit Modal based on modal's selectedSector
        $subSectors = $this->getAllowedSubSectors($this->selectedSector);

        // Subsectors for Table Filter based on filterSector
        $filterSubSectors = $this->getAllowedSubSectors($this->filterSector);

        $documentTypes = \App\Models\TblRefDocumentTypes::where('sector', $this->selectedSector)
            ->where('sub_sector', $this->selectedSubSector)
            ->where('is_active', 'Y') // Filter by is_active column
            ->get();
        // END

        $departments = TblRefDepartment::orderBy('department_name')->get();

        // Resolve the current user's department name for the read-only Source field display
        $userDeptId   = auth()->user()->office ?? null;
        $userDeptName = $userDeptId
            ? (TblRefDepartment::where('department_id', $userDeptId)->value('department_name') ?? 'Unknown Department')
            : 'N/A';

        $availableYears = $this->getAvailableYears();

        return view('livewire.document-archive-table', compact('subSectors', 'filterSubSectors', 'documentTypes', 'allowedSectors', 'departments', 'userDeptName', 'availableYears'), [
            'data' => $data,
            'currentPage' => $data->currentPage(),
            'totalPages' => $data->lastPage(),
            'totalRecords' => $data->total(),
            'noRecords' => $data->isEmpty(),
            'hasNoSectorPermissions' => $sectorPermissions->isEmpty(),
            'noFilterSectorSelected' => false,
        ]);
    }
}
