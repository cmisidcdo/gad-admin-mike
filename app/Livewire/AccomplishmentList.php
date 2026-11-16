<?php

namespace App\Livewire;

use App\Models\GadAccomplishment;
use App\Models\GadAccomplishmentStatusLog;
use App\Models\GadPlan;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AccomplishmentList extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $filterYear = '';
    public $filterStatus = '';
    public $filterDepartment = '';

    // Add modal
    public $showAdd = false;
    public $year = '';
    public $totalOfficeBudget = '';
    public $departmentId = '';

    // Delete modal
    public $deleteId = null;

    // Track modal
    public $trackId = null;

    // Attach link modal
    public $attachLinkModal = false;
    public $attachReportId = null;
    public $selectedGpbId = '';

    // Admin Status modal
    public $statusId = null;
    public $newStatus = '';
    public $statusRemarks = '';
    public $reviewerFile;

    public function updatingFilterYear() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDepartment() { $this->resetPage(); }

    public function openAdd()
    {
        $this->reset(['year', 'totalOfficeBudget']);
        $this->departmentId = auth()->user()->office ?? '';
        $this->resetValidation();
        $this->showAdd = true;
    }

    public function save()
    {
        $rules = [
            'year' => 'required|integer|digits:4',
            'totalOfficeBudget' => 'required|numeric|min:0',
        ];

        if ((int) auth()->user()->user_role_id === 1) {
            $rules['departmentId'] = 'required';
        }

        $this->validate($rules, [
            'year.required' => 'Please select a year.',
            'totalOfficeBudget.required' => 'Total Office Budget is required.',
            'departmentId.required' => 'Please select an Office / Department.',
        ]);

        $deptId = (int) auth()->user()->user_role_id === 1
            ? $this->departmentId
            : (auth()->user()->office ?? null);

        $ar = GadAccomplishment::create([
            'year' => $this->year,
            'total_office_budget' => $this->totalOfficeBudget,
            'status' => 'Encoding process',
            'status_date' => now(),
            'created_by' => auth()->id(),
            'department_id' => $deptId,
        ]);

        GadAccomplishmentStatusLog::create([
            'gad_accomplishment_id' => $ar->id,
            'status' => $ar->status,
            'remarks' => 'Accomplishment report created.',
            'changed_by' => auth()->id(),
        ]);

        return redirect()->route('accomplishment.show', $ar->id);
    }

    public function confirmDelete($id) { $this->deleteId = $id; }

    public function delete()
    {
        $ar = GadAccomplishment::findOrFail($this->deleteId);
        $user = auth()->user();
        $isAdmin = (int) $user->user_role_id === 1;

        if (!$isAdmin && $ar->department_id !== $user->office) {
            abort(403, 'Unauthorized access to other office records.');
        }

        abort_unless($ar->status === 'Encoding process' || $isAdmin, 403);
        $ar->delete();
        $this->deleteId = null;
        session()->flash('message', 'Accomplishment report deleted.');
    }

    public function track($id) { $this->trackId = $id; }

    public function openAttachLink($id)
    {
        $this->attachReportId = $id;
        $ar = GadAccomplishment::findOrFail($id);
        $this->selectedGpbId = $ar->attached_to_gpb_id ?? '';
        $this->attachLinkModal = true;
    }

    public function saveAttachLink()
    {
        $ar = GadAccomplishment::findOrFail($this->attachReportId);
        $ar->update(['attached_to_gpb_id' => $this->selectedGpbId ?: null]);
        $this->attachLinkModal = false;
        session()->flash('message', 'Linked GPB plan updated.');
    }

    /* Admin actions */
    private function ensureAdmin(): void
    {
        abort_unless((int) auth()->user()->user_role_id === 1, 403);
    }

    public function openStatus($id)
    {
        $this->ensureAdmin();
        $ar = GadAccomplishment::findOrFail($id);
        $this->reset(['statusRemarks', 'reviewerFile']);
        $this->resetValidation();
        $this->statusId = $id;
        $this->newStatus = $ar->status;
    }

    public function saveStatus()
    {
        $this->ensureAdmin();
        $this->validate([
            'newStatus' => 'required|in:' . implode(',', GadAccomplishment::STATUSES),
            'statusRemarks' => 'nullable|string|max:500',
            'reviewerFile' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:' . config('gad.upload_max_kb'),
        ]);

        $ar = GadAccomplishment::findOrFail($this->statusId);
        $data = [
            'status' => $this->newStatus,
            'status_remarks' => $this->statusRemarks ?: null,
            'status_date' => now(),
        ];
        if ($this->reviewerFile) {
            $data['reviewer_attachment'] = $this->reviewerFile->store('gad-accomplishments/reviewer', 'public');
        }
        $ar->update($data);

        GadAccomplishmentStatusLog::create([
            'gad_accomplishment_id' => $ar->id,
            'status' => $this->newStatus,
            'remarks' => $this->statusRemarks ?: null,
            'changed_by' => auth()->id(),
        ]);

        $this->reset(['statusId', 'statusRemarks', 'reviewerFile']);
        session()->flash('message', 'Status updated.');
    }

    public function render()
    {
        $user = auth()->user();
        $isAdmin = (int) $user->user_role_id === 1;

        $reports = GadAccomplishment::with(['attributedPrograms', 'gpbPlan', 'department'])
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('department_id', $user->office)
                        ->orWhere('created_by', $user->id);
                });
            })
            ->when($isAdmin && $this->filterDepartment, fn ($q) => $q->where('department_id', $this->filterDepartment))
            ->when($this->filterYear, fn ($q) => $q->where('year', $this->filterYear))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->latest('id')
            ->paginate(10);

        return view('livewire.accomplishment-list', [
            'reports' => $reports,
            'years' => range((int) date('Y') + 1, 2020),
            'statuses' => GadAccomplishment::STATUSES,
            'trackReport' => $this->trackId ? GadAccomplishment::with('statusLogs')->find($this->trackId) : null,
            'gpbPlans' => GadPlan::when(!$isAdmin, fn ($q) => $q->where('department_id', $user->office))
                ->orderByDesc('year')->get(),
            'departments' => $isAdmin ? \App\Models\TblRefDepartment::orderBy('department_name')->get() : collect(),
            'isAdmin' => $isAdmin,
        ]);
    }
}

