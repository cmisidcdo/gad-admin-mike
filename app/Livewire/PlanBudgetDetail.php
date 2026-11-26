<?php

namespace App\Livewire;

use App\Models\GadPlan;
use App\Models\GadPlanAttributedProgram;
use App\Models\GadPlanEntry;
use Livewire\Component;
use Livewire\WithFileUploads;

class PlanBudgetDetail extends Component
{
    use WithFileUploads, \App\Livewire\Concerns\CleansMoney;

    /** Amount fields shown with commas (stripped on the server). */
    protected array $moneyFields = ['officeBudget', 'entry.moe', 'entry.ps', 'entry.co', 'attr.total_annual_budget'];

    public GadPlan $plan;

    // 'org' | 'attributed' | '' (both collapsed)
    public $openForm = 'org';

    public $categoryFilter = '';

    // Office budget inline edit
    public $editingBudget = false;
    public $officeBudget;

    // Org/Client-focused form
    public $entryId = null;
    public array $entry = [];

    // Attributed form
    public $attrId = null;
    public array $attr = [];

    // Modals
    public $detailsId = null;
    public $deleteType = null; // entry | attributed
    public $deleteId = null;
    public $attachType = null;
    public $attachId = null;
    public $file;
    public $selectedArId = '';
    public $showAr = false;

    // Field Remarks modal (Admin can add/edit, user can view)
    public $remarkModal = false;
    public $remarkType = null; // 'entry' | 'attributed'
    public $remarkRecordId = null;
    public $remarkField = null;
    public $remarkFieldLabel = '';
    public $remarkText = '';
    public $currentRemark = null;

    public function mount(GadPlan $plan)
    {
        $user = auth()->user();
        $isAdmin = (int) $user->user_role_id === 1;

        if (!$isAdmin && $plan->department_id !== $user->office && $plan->created_by !== $user->id) {
            abort(403, 'Unauthorized access: You can only view your own office reports.');
        }

        $this->plan = $plan;
        $this->officeBudget = $plan->total_office_budget;
        $this->resetEntry();
        $this->resetAttr();
    }

    /* ---------- helpers ---------- */

    private function resetEntry()
    {
        $this->entryId = null;
        $this->entry = [
            'focused' => '', 'gender_issue' => '', 'title_description' => '', 'supporting_statistics' => '',
            'statistics_source' => '', 'ppa_sector' => [], 'gad_objective' => '', 'lgu_program' => '',
            'activity_category' => '', 'other_activity_category' => '', 'gad_activity' => '',
            'date_of_implementation' => '', 'performance_indicator' => '', 'moe' => '', 'ps' => '', 'co' => '',
        ];
        $this->resetValidation();
    }

    private function resetAttr()
    {
        $this->attrId = null;
        $this->attr = ['ppa_sector' => [], 'title' => '', 'checklist' => '', 'hgdg_score' => '', 'total_annual_budget' => ''];
        $this->resetValidation();
    }

    private function refreshPlan()
    {
        $this->plan = GadPlan::findOrFail($this->plan->id);
    }

    public function toggleForm($form)
    {
        $this->openForm = $this->openForm === $form ? '' : $form;
        $this->categoryFilter = '';
    }

    private function ensureEditable(): void
    {
        $isAdmin = (int) auth()->user()->user_role_id === 1;
        if (!$isAdmin && $this->plan->status !== 'Encoding process') {
            abort(403, 'Editing is locked. You can only edit when the status is Encoding process.');
        }
    }

    public function canEdit(): bool
    {
        return (int) auth()->user()->user_role_id === 1 || $this->plan->status === 'Encoding process';
    }

    /* ---------- office budget ---------- */

    public function saveBudget()
    {
        $this->ensureEditable();
        $this->validate(['officeBudget' => 'required|numeric|min:0']);
        $this->plan->update(['total_office_budget' => $this->officeBudget]);
        $this->editingBudget = false;
        $this->refreshPlan();
    }

    /* ---------- AR (Accomplishment Report) link ---------- */

    public function openAr()
    {
        $this->ensureEditable();
        $this->resetValidation();
        $this->selectedArId = optional($this->plan->accomplishmentReport)->id ?? '';
        $this->showAr = true;
    }

    public function saveAr()
    {
        $this->ensureEditable();
        $this->validate(
            ['selectedArId' => 'required|integer'],
            ['selectedArId.required' => 'Please select an Accomplishment Report.']
        );
        $this->plan->attachReport((int) $this->selectedArId);
        $this->reset(['selectedArId', 'showAr']);
        $this->refreshPlan();
    }

    public function detachAr()
    {
        $this->ensureEditable();
        $this->plan->detachReport();
        $this->refreshPlan();
    }

    /** Copy a previous GAD activity from the attached AR into the input form. */
    public function useArEntry($id)
    {
        $this->ensureEditable();
        $ar = $this->plan->accomplishmentReport;
        abort_unless($ar, 404);
        $row = $ar->entries()->findOrFail($id);

        $this->resetEntry();
        $this->entry['focused'] = in_array($row->focus, config('gad.focused'), true) ? $row->focus : '';
        $this->entry['gender_issue'] = (string) $row->gender_issue;
        $this->entry['gad_objective'] = (string) $row->gad_objective;
        $this->entry['lgu_program'] = (string) $row->lgu_program;
        $this->entry['gad_activity'] = (string) $row->gad_activity;
        $this->entry['performance_indicator'] = (string) $row->performance_indicator;
        $this->entry['moe'] = $row->approved_gad_budget;
        $this->openForm = 'org';
    }

    /* ---------- org/client-focused entries ---------- */

    public function saveEntry()
    {
        $this->ensureEditable();
        $this->validate([
            'entry.focused' => 'required|in:' . implode(',', config('gad.focused')),
            'entry.gender_issue' => 'required|string',
            'entry.title_description' => 'required|string',
            'entry.supporting_statistics' => 'nullable|string',
            'entry.statistics_source' => 'nullable|string',
            'entry.ppa_sector' => 'required|array|min:1',
            'entry.ppa_sector.*' => 'in:' . implode(',', config('gad.ppa_sectors')),
            'entry.gad_objective' => 'required|string',
            'entry.lgu_program' => 'required|string',
            'entry.activity_category' => 'required|string',
            'entry.other_activity_category' => 'nullable|string|max:255',
            'entry.gad_activity' => 'required|string',
            'entry.date_of_implementation' => 'required|date',
            'entry.performance_indicator' => 'required|string',
            'entry.moe' => 'nullable|numeric|min:0',
            'entry.ps' => 'nullable|numeric|min:0',
            'entry.co' => 'nullable|numeric|min:0',
        ], [
            'entry.*.required' => 'Required.',
        ]);

        $data = $this->entry;
        $data['ppa_sector'] = implode(', ', $data['ppa_sector']);
        foreach (['moe', 'ps', 'co'] as $k) {
            $data[$k] = $data[$k] === '' || $data[$k] === null ? 0 : $data[$k];
        }

        if ($this->entryId) {
            $this->plan->entries()->findOrFail($this->entryId)->update($data);
        } else {
            $this->plan->entries()->create($data);
        }

        $this->resetEntry();
        $this->refreshPlan();
    }

    public function editEntry($id)
    {
        $this->ensureEditable();
        $row = $this->plan->entries()->findOrFail($id);
        $this->entryId = $row->id;
        $this->entry = collect($row->only(array_keys($this->entry)))
            ->map(fn ($v) => $v ?? '')
            ->all();
        $this->entry['ppa_sector'] = array_values(array_filter(array_map('trim', explode(',', (string) $row->ppa_sector))));
        $this->entry['date_of_implementation'] = optional($row->date_of_implementation)->format('Y-m-d') ?? '';
        $this->openForm = 'org';
    }

    public function clearEntry() { $this->resetEntry(); }

    /* ---------- attributed programs ---------- */

    public function saveAttr()
    {
        $this->ensureEditable();
        $this->validate([
            'attr.ppa_sector' => 'required|array|min:1',
            'attr.ppa_sector.*' => 'in:' . implode(',', config('gad.ppa_sectors')),
            'attr.title' => 'required|string|max:255',
            'attr.checklist' => 'required|string',
            'attr.hgdg_score' => 'required|numeric|min:0|max:20',
            'attr.total_annual_budget' => 'required|numeric|min:0',
        ], [
            'attr.*.required' => 'Required.',
        ]);

        $data = $this->attr;
        $data['ppa_sector'] = implode(', ', $data['ppa_sector']);

        if ($this->attrId) {
            $this->plan->attributedPrograms()->findOrFail($this->attrId)->update($data);
        } else {
            $this->plan->attributedPrograms()->create($data);
        }

        $this->resetAttr();
        $this->refreshPlan();
    }

    public function editAttr($id)
    {
        $this->ensureEditable();
        $row = $this->plan->attributedPrograms()->findOrFail($id);
        $this->attrId = $row->id;
        $this->attr = collect($row->only(array_keys($this->attr)))->map(fn ($v) => $v ?? '')->all();
        $this->attr['ppa_sector'] = array_values(array_filter(array_map('trim', explode(',', (string) $row->ppa_sector))));
        $this->openForm = 'attributed';
    }

    public function clearAttr() { $this->resetAttr(); }

    /* ---------- details / delete / attach ---------- */

    public function showDetails($id) { $this->detailsId = $id; }

    public function confirmDelete($type, $id)
    {
        $this->ensureEditable();
        $this->deleteType = $type;
        $this->deleteId = $id;
    }

    public function delete()
    {
        $this->ensureEditable();
        if ($this->deleteType === 'entry') {
            $this->plan->entries()->findOrFail($this->deleteId)->delete();
        } elseif ($this->deleteType === 'attributed') {
            $this->plan->attributedPrograms()->findOrFail($this->deleteId)->delete();
        }
        $this->reset(['deleteType', 'deleteId']);
        $this->refreshPlan();
    }

    public function openAttach($type, $id)
    {
        $this->ensureEditable();
        $this->reset('file');
        $this->resetValidation();
        $this->attachType = $type;
        $this->attachId = $id;
    }

    public function saveAttach()
    {
        $this->ensureEditable();
        $this->validate(['file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png,xls,xlsx|max:' . config('gad.upload_max_kb')]);

        $row = $this->attachType === 'entry'
            ? $this->plan->entries()->findOrFail($this->attachId)
            : $this->plan->attributedPrograms()->findOrFail($this->attachId);

        $row->update(['attachment' => $this->file->store('gad-plans/' . $this->plan->id, 'public')]);
        $this->reset(['file', 'attachType', 'attachId']);
    }

    /* ---------- Field Remarks (Admin add/edit, User read) ---------- */

    public function openFieldRemark(string $type, int $recordId, string $field, string $label)
    {
        $this->remarkType = $type;
        $this->remarkRecordId = $recordId;
        $this->remarkField = $field;
        $this->remarkFieldLabel = $label;

        $modelClass = $type === 'entry' ? GadPlanEntry::class : GadPlanAttributedProgram::class;
        $existing = \App\Models\GadFieldRemark::where('remarkable_type', $modelClass)
            ->where('remarkable_id', $recordId)
            ->where('field_name', $field)
            ->first();

        $this->currentRemark = $existing;
        $this->remarkText = $existing?->remarks ?? '';
        $this->resetValidation();
        $this->remarkModal = true;
    }

    public function saveFieldRemark()
    {
        abort_unless((int) auth()->user()->user_role_id === 1, 403, 'Only administrators can add or edit remarks.');

        $this->validate([
            'remarkText' => 'required|string|max:1000',
        ], [
            'remarkText.required' => 'Please enter a remark.',
        ]);

        $modelClass = $this->remarkType === 'entry' ? GadPlanEntry::class : GadPlanAttributedProgram::class;

        \App\Models\GadFieldRemark::updateOrCreate(
            [
                'remarkable_type' => $modelClass,
                'remarkable_id' => $this->remarkRecordId,
                'field_name' => $this->remarkField,
            ],
            [
                'remarks' => $this->remarkText,
                'admin_id' => auth()->id(),
            ]
        );

        $this->remarkModal = false;
        $this->refreshPlan();
    }

    public function deleteFieldRemark()
    {
        abort_unless((int) auth()->user()->user_role_id === 1, 403, 'Only administrators can delete remarks.');

        if ($this->currentRemark) {
            $this->currentRemark->delete();
        }

        $this->remarkModal = false;
        $this->refreshPlan();
    }

    public function render()
    {
        $entries = $this->plan->entries()
            ->with('remarks')
            ->when($this->categoryFilter && $this->openForm === 'org', fn ($q) => $q->where('focused', $this->categoryFilter))
            ->orderBy('id')->get();

        $attributed = $this->plan->attributedPrograms()
            ->with('remarks')
            ->when($this->categoryFilter && $this->openForm === 'attributed', fn ($q) => $q->where('ppa_sector', 'like', '%' . $this->categoryFilter . '%'))
            ->orderBy('id')->get();

        $linkedAr = $this->plan->accomplishmentReport;
        if ($linkedAr) {
            $linkedAr->load('entries');
        }

        return view('livewire.plan-budget-detail', [
            'linkedAr' => $linkedAr,
            'availableReports' => $this->showAr ? $this->plan->linkableReports()->get() : collect(),
            'entries' => $entries,
            'attributed' => $attributed,
            'detailEntry' => $this->detailsId ? GadPlanEntry::where('gad_plan_id', $this->plan->id)->find($this->detailsId) : null,
            'totalGad' => $this->plan->total_gad_budget,
            'pct' => $this->plan->gad_percentage,
        ]);
    }
}

