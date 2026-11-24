<?php

namespace App\Livewire;

use App\Models\GadAccomplishment;
use App\Models\GadAccomplishmentAttributedProgram;
use App\Models\GadAccomplishmentEntry;
use Livewire\Component;
use Livewire\WithFileUploads;

class AccomplishmentDetail extends Component
{
    use WithFileUploads, \App\Livewire\Concerns\CleansMoney;

    /** Amount fields shown with commas (stripped on the server). */
    protected array $moneyFields = ['officeBudget', 'entry.approved_gad_budget', 'entry.actual_cost', 'attr.total_annual_cost', 'attr.gad_attributed_cost'];

    public GadAccomplishment $report;

    // 'org' | 'attributed' | ''
    public $openForm = 'org';
    public $categoryFilter = '';

    // Budget inline edit
    public $editingBudget = false;
    public $officeBudget;

    // Org/Client Form
    public $entryId = null;
    public array $entry = [];

    // Attributed Form
    public $attrId = null;
    public array $attr = [];

    // Modals
    public $detailsId = null;
    public $deleteType = null;
    public $deleteId = null;
    public $attachType = null;
    public $attachId = null;
    public $file;
    public $arFile;
    public $showAr = false;

    // Field Remarks modal
    public $remarkModal = false;
    public $remarkType = null; // 'entry' | 'attributed'
    public $remarkRecordId = null;
    public $remarkField = null;
    public $remarkFieldLabel = '';
    public $remarkText = '';
    public $currentRemark = null;

    public function mount(GadAccomplishment $report)
    {
        $user = auth()->user();
        $isAdmin = (int) $user->user_role_id === 1;

        if (!$isAdmin && $report->department_id !== $user->office && $report->created_by !== $user->id) {
            abort(403, 'Unauthorized access: You can only view your own office reports.');
        }

        $this->report = $report;
        $this->officeBudget = $report->total_office_budget;
        $this->resetEntry();
        $this->resetAttr();
    }

    private function resetEntry()
    {
        $this->entryId = null;
        $this->entry = [
            'focus' => '',
            'gender_issue' => '',
            'gad_objective' => '',
            'lgu_program' => '',
            'gad_activity' => '',
            'performance_indicator' => '',
            'actual_results' => '',
            'approved_gad_budget' => '',
            'actual_cost' => '',
            'variance_remarks' => '',
        ];
        $this->resetValidation();
    }

    private function resetAttr()
    {
        $this->attrId = null;
        $this->attr = [
            'title' => '',
            'checklist_score_desc' => '',
            'hgdg_score' => '',
            'total_annual_cost' => '',
            'gad_attributed_cost' => '',
            'variance_remarks' => '',
        ];
        $this->resetValidation();
    }

    private function refreshReport()
    {
        $this->report = GadAccomplishment::findOrFail($this->report->id);
    }

    public function toggleForm($form)
    {
        $this->openForm = $this->openForm === $form ? '' : $form;
        $this->categoryFilter = '';
    }

    private function ensureEditable(): void
    {
        $isAdmin = (int) auth()->user()->user_role_id === 1;
        if (!$isAdmin && $this->report->status !== 'Encoding process') {
            abort(403, 'Editing is locked. You can only edit when the status is Encoding process.');
        }
    }

    public function canEdit(): bool
    {
        return (int) auth()->user()->user_role_id === 1 || $this->report->status === 'Encoding process';
    }

    /* Office budget inline edit */
    public function saveBudget()
    {
        $this->ensureEditable();
        $this->validate(['officeBudget' => 'required|numeric|min:0']);
        $this->report->update(['total_office_budget' => $this->officeBudget]);
        $this->editingBudget = false;
        $this->refreshReport();
    }

    /* AR attachment */
    public function saveAr()
    {
        $this->ensureEditable();
        $this->validate(['arFile' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:' . config('gad.upload_max_kb')]);
        $this->report->update(['ar_attachment' => $this->arFile->store('gad-accomplishments/ar', 'public')]);
        $this->reset(['arFile', 'showAr']);
        $this->refreshReport();
    }

    /* Org entry */
    public function saveEntry()
    {
        $this->ensureEditable();
        $this->validate([
            'entry.focus' => 'nullable|string',
            'entry.gender_issue' => 'required|string',
            'entry.gad_objective' => 'required|string',
            'entry.lgu_program' => 'required|string',
            'entry.gad_activity' => 'required|string',
            'entry.performance_indicator' => 'required|string',
            'entry.actual_results' => 'nullable|string',
            'entry.approved_gad_budget' => 'nullable|numeric|min:0',
            'entry.actual_cost' => 'nullable|numeric|min:0',
            'entry.variance_remarks' => 'nullable|string',
        ], [
            'entry.*.required' => 'Required.',
        ]);

        $data = $this->entry;
        $data['approved_gad_budget'] = $data['approved_gad_budget'] ?: 0;
        $data['actual_cost'] = $data['actual_cost'] ?: 0;

        if ($this->entryId) {
            $this->report->entries()->findOrFail($this->entryId)->update($data);
        } else {
            $this->report->entries()->create($data);
        }

        $this->resetEntry();
        $this->refreshReport();
    }

    public function editEntry($id)
    {
        $this->ensureEditable();
        $row = $this->report->entries()->findOrFail($id);
        $this->entryId = $row->id;
        $this->entry = collect($row->only(array_keys($this->entry)))->map(fn ($v) => $v ?? '')->all();
        $this->openForm = 'org';
    }

    public function clearEntry() { $this->resetEntry(); }

    /* Attributed program entry */
    public function saveAttr()
    {
        $this->ensureEditable();
        $this->validate([
            'attr.title' => 'required|string|max:255',
            'attr.checklist_score_desc' => 'nullable|string',
            'attr.hgdg_score' => 'nullable|numeric|min:0|max:20',
            'attr.total_annual_cost' => 'nullable|numeric|min:0',
            'attr.gad_attributed_cost' => 'nullable|numeric|min:0',
            'attr.variance_remarks' => 'nullable|string',
        ], [
            'attr.*.required' => 'Required.',
        ]);

        $data = $this->attr;
        $data['hgdg_score'] = $data['hgdg_score'] ?: 0;
        $data['total_annual_cost'] = $data['total_annual_cost'] ?: 0;
        $data['gad_attributed_cost'] = $data['gad_attributed_cost'] ?: 0;

        if ($this->attrId) {
            $this->report->attributedPrograms()->findOrFail($this->attrId)->update($data);
        } else {
            $this->report->attributedPrograms()->create($data);
        }

        $this->resetAttr();
        $this->refreshReport();
    }

    public function editAttr($id)
    {
        $this->ensureEditable();
        $row = $this->report->attributedPrograms()->findOrFail($id);
        $this->attrId = $row->id;
        $this->attr = collect($row->only(array_keys($this->attr)))->map(fn ($v) => $v ?? '')->all();
        $this->openForm = 'attributed';
    }

    public function clearAttr() { $this->resetAttr(); }

    /* Delete / Details / Attach */
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
            $this->report->entries()->findOrFail($this->deleteId)->delete();
        } elseif ($this->deleteType === 'attributed') {
            $this->report->attributedPrograms()->findOrFail($this->deleteId)->delete();
        }
        $this->reset(['deleteType', 'deleteId']);
        $this->refreshReport();
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
            ? $this->report->entries()->findOrFail($this->attachId)
            : $this->report->attributedPrograms()->findOrFail($this->attachId);

        $row->update(['attachment' => $this->file->store('gad-accomplishments/' . $this->report->id, 'public')]);
        $this->reset(['file', 'attachType', 'attachId']);
    }

    /* ---------- Field Remarks (Admin add/edit, User read) ---------- */

    public function openFieldRemark(string $type, int $recordId, string $field, string $label)
    {
        $this->remarkType = $type;
        $this->remarkRecordId = $recordId;
        $this->remarkField = $field;
        $this->remarkFieldLabel = $label;

        $modelClass = $type === 'entry' ? GadAccomplishmentEntry::class : GadAccomplishmentAttributedProgram::class;
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

        $modelClass = $this->remarkType === 'entry' ? GadAccomplishmentEntry::class : GadAccomplishmentAttributedProgram::class;

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
        $this->refreshReport();
    }

    public function deleteFieldRemark()
    {
        abort_unless((int) auth()->user()->user_role_id === 1, 403, 'Only administrators can delete remarks.');

        if ($this->currentRemark) {
            $this->currentRemark->delete();
        }

        $this->remarkModal = false;
        $this->refreshReport();
    }

    public function render()
    {
        $entries = $this->report->entries()
            ->with('remarks')
            ->when($this->categoryFilter && $this->openForm === 'org', fn ($q) => $q->where('focus', $this->categoryFilter))
            ->orderBy('id')->get();

        $attributed = $this->report->attributedPrograms()
            ->with('remarks')
            ->orderBy('id')->get();

        return view('livewire.accomplishment-detail', [
            'entries' => $entries,
            'attributed' => $attributed,
            'detailEntry' => $this->detailsId ? GadAccomplishmentEntry::where('gad_accomplishment_id', $this->report->id)->find($this->detailsId) : null,
            'totalGad' => $this->report->total_gad_expenditure,
            'pct' => $this->report->gad_percentage,
        ]);
    }
}

