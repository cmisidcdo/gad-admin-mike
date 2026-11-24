<?php

namespace App\Livewire;

use App\Models\GadPlan;
use App\Models\GadPlanStatusLog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PlanBudgetList extends Component
{
    use WithPagination, WithFileUploads, \App\Livewire\Concerns\CleansMoney;

    /** Amount fields shown with commas (stripped on the server). */
    protected array $moneyFields = ['totalOfficeBudget'];

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

    // Attach AR modal (links an existing Accomplishment Report)
    public $attachId = null;
    public $selectedArId = '';

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

        $plan = GadPlan::create([
            'year' => $this->year,
            'total_office_budget' => $this->totalOfficeBudget,
            'status' => 'Encoding process',
            'status_date' => now(),
            'created_by' => auth()->id(),
            'department_id' => $deptId,
        ]);

        GadPlanStatusLog::create([
            'gad_plan_id' => $plan->id,
            'status' => $plan->status,
            'remarks' => 'Plan created.',
            'changed_by' => auth()->id(),
        ]);

        return redirect()->route('plan-budget.show', $plan->id);
    }

    public function confirmDelete($id) { $this->deleteId = $id; }

    public function delete()
    {
        $plan = GadPlan::findOrFail($this->deleteId);
        $user = auth()->user();
        $isAdmin = (int) $user->user_role_id === 1;

        if (!$isAdmin && $plan->department_id !== $user->office) {
            abort(403, 'Unauthorized access to other office records.');
        }

        abort_unless($plan->status === 'Encoding process' || $isAdmin, 403);
        $plan->delete();
        $this->deleteId = null;
        session()->flash('message', 'Plan deleted.');
    }

    public function track($id) { $this->trackId = $id; }

    private function authorizedPlan($id): GadPlan
    {
        $plan = GadPlan::findOrFail($id);
        $user = auth()->user();

        if ((int) $user->user_role_id !== 1 && $plan->department_id !== $user->office && $plan->created_by !== $user->id) {
            abort(403, 'Unauthorized access to other office records.');
        }

        return $plan;
    }

    public function openAttach($id)
    {
        $plan = $this->authorizedPlan($id);
        $this->resetValidation();
        $this->selectedArId = optional($plan->accomplishmentReport)->id ?? '';
        $this->attachId = $id;
    }

    public function saveAttach()
    {
        $this->validate(
            ['selectedArId' => 'required|integer'],
            ['selectedArId.required' => 'Please select an Accomplishment Report.']
        );

        $plan = $this->authorizedPlan($this->attachId);
        $plan->attachReport((int) $this->selectedArId);

        $this->reset(['attachId', 'selectedArId']);
        session()->flash('message', 'Accomplishment Report attached to the Plan and Budget.');
    }

    public function detachAr($id)
    {
        $this->authorizedPlan($id)->detachReport();
        session()->flash('message', 'Accomplishment Report detached.');
    }

    /* ---------- Admin-only: status change & reviewer attachment ---------- */

    public $statusId = null;
    public $newStatus = '';
    public $statusRemarks = '';
    public $reviewerFile;

    private function ensureAdmin(): void
    {
        abort_unless((int) auth()->user()->user_role_id === 1, 403);
    }

    public function openStatus($id)
    {
        $this->ensureAdmin();
        $plan = GadPlan::findOrFail($id);
        $this->reset(['statusRemarks', 'reviewerFile']);
        $this->resetValidation();
        $this->statusId = $id;
        $this->newStatus = $plan->status;
    }

    public function saveStatus()
    {
        $this->ensureAdmin();
        $this->validate([
            'newStatus' => 'required|in:' . implode(',', GadPlan::STATUSES),
            'statusRemarks' => 'nullable|string|max:500',
            'reviewerFile' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:' . config('gad.upload_max_kb'),
        ]);

        $plan = GadPlan::findOrFail($this->statusId);
        $data = [
            'status' => $this->newStatus,
            'status_remarks' => $this->statusRemarks ?: null,
            'status_date' => now(),
        ];
        if ($this->reviewerFile) {
            $data['reviewer_attachment'] = $this->reviewerFile->store('gad-plans/reviewer', 'public');
        }
        $plan->update($data);

        GadPlanStatusLog::create([
            'gad_plan_id' => $plan->id,
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

        $plans = GadPlan::with(['attributedPrograms', 'department', 'accomplishmentReport'])
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

        return view('livewire.plan-budget-list', [
            'plans' => $plans,
            'years' => range((int) date('Y') + 1, 2020),
            'statuses' => GadPlan::STATUSES,
            'trackPlan' => $this->trackId ? GadPlan::with('statusLogs')->find($this->trackId) : null,
            'departments' => $isAdmin ? \App\Models\TblRefDepartment::orderBy('department_name')->get() : collect(),
            'isAdmin' => $isAdmin,
            'availableReports' => $this->attachId
                ? GadPlan::findOrFail($this->attachId)->linkableReports()->get()
                : collect(),
        ]);
    }
}

