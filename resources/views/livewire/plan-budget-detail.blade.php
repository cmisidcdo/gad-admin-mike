<div>
    @php
    $statusClass = match($plan->status) {
        'Returned to Office' => 'returned',
        'Submitted to CPDO' => 'submitted',
        'Endorsed to DILG' => 'endorsed',
        default => '',
    };
    $cfg = config('gad');
    $field = fn($name) => $errors->has($name) ? 'border-color:#e00000' : '';
    @endphp

    <div class="gad-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <a href="{{ route('plan-budget') }}" class="btn-gad-back"><i class="bi bi-arrow-left"></i> Back</a>
                    <a href="{{ route('plan-budget.print', $plan->id) }}" target="_blank" class="btn-gad-print"><i class="bi bi-printer-fill"></i> Printable Report</a>
                    <a href="{{ route('plan-budget.excel', $plan->id) }}" class="btn-gad-excel"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Download Excel</a>
                </div>
                <div class="gad-title plain mb-2">
                    GAD Plan and Budget FY {{ $plan->year }}
                    @if ($plan->department)
                    <span class="badge bg-light text-primary border ms-2" style="font-size:11.5px; font-weight:600;">
                        <i class="bi bi-building me-1"></i>{{ $plan->department->department_name }}
                    </span>
                    @endif
                </div>

                <div class="mb-2 d-flex align-items-center">
                    <span class="gad-label me-1">Total Office Budget</span>
                    @if ($editingBudget && $this->canEdit())
                    <input type="number" step="0.01" min="0" class="gad-input me-1" style="width:150px" wire:model="officeBudget">
                    <button class="btn-xs save" wire:click="saveBudget"><i class="bi bi-check-lg"></i></button>
                    <button class="btn-xs clear ms-1" wire:click="$set('editingBudget', false)"><i class="bi bi-x-lg"></i></button>
                    @error('officeBudget') <span class="error-text ms-1">{{ $message }}</span> @enderror
                    @else
                    <span>Php {{ number_format($plan->total_office_budget, 2) }}</span>
                    @if ($this->canEdit())
                    <i class="bi bi-pencil-fill ms-2 text-primary" style="cursor:pointer;font-size:11px" title="Edit Budget" wire:click="$set('editingBudget', true)"></i>
                    @endif
                    @endif
                </div>
                <div>
                    <span class="gad-label me-1">Total Office GAD Budget:</span>
                    <span class="budget-red" style="background:#fdeeee;padding:3px 8px;border:1px solid #f3c9c9">Php {{ number_format($totalGad, 2) }} ({{ number_format($pct, 2) }}%)</span>
                </div>
            </div>
            <div class="text-end">
                <span class="badge-status {{ $statusClass }}">{{ strtoupper($plan->status) }}</span>
                <div class="mt-4">
                    <span class="gad-label">Attachment of AR</span>
                    @if ($plan->ar_attachment)
                    <a class="ms-1" target="_blank" href="{{ asset('storage/' . $plan->ar_attachment) }}" style="font-size:11px"><i class="bi bi-paperclip"></i> View AR</a>
                    @elseif ($this->canEdit())
                    <a href="#" class="ms-1 fst-italic text-danger" style="font-size:11px" wire:click.prevent="$set('showAr', true)">Click here to attach AR</a>
                    @else
                    <span class="ms-1 text-muted fst-italic" style="font-size:11px">No AR Attached</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ORG / CLIENT FOCUSED FORM --}}
        @if ($this->canEdit())
        <button type="button" class="form-bar org mt-3 {{ $openForm === 'org' ? 'open' : '' }}" wire:click="toggleForm('org')">
            <span>Org-focused and Client-focused Input Form</span>
            <i class="bi {{ $openForm === 'org' ? 'bi-dash-lg' : 'bi-plus-lg' }}"></i>
        </button>
        @if ($openForm === 'org')
        <div class="form-panel form-grid scrollable-form">
            {{-- Row 1: 10 upper fields --}}
        <div class="figma-gpb-form-row">
                <div class="figma-gpb-col" style="flex:0 0 110px;">
                    <label>Focused <span class="req">*</span></label>
                    <select class="gad-select" style="{{ $field('entry.focused') }}" wire:model="entry.focused">
                        <option value="">Select...</option>
                        @foreach ($cfg['focused'] as $o)<option>{{ $o }}</option>@endforeach
                    </select>
                </div>

                <div class="figma-gpb-col" style="flex:0 0 130px;">
                    <label>Gender Issue or GAD Mandate <span class="req">*</span></label>
                    <textarea class="gad-input" placeholder="Enter Text..." style="{{ $field('entry.gender_issue') }}" wire:model="entry.gender_issue"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:1 1 160px;">
                    <label>Title/ Description of Gender Issue or GAD Mandate <span class="req">*</span></label>
                    <textarea class="gad-input" placeholder="Enter Text..." style="{{ $field('entry.title_description') }}" wire:model="entry.title_description"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:1 1 130px;">
                    <label>Supporting Statistics Data</label>
                    <textarea class="gad-input" placeholder="Enter Text..." wire:model="entry.supporting_statistics"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:1 1 130px;">
                    <label>Source of Supporting Statistics Data</label>
                    <textarea class="gad-input" placeholder="Enter Text..." wire:model="entry.statistics_source"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:0 0 115px;">
                    <label>PPA Sectors <span class="req">*</span></label>
                    <select class="gad-select" style="{{ $field('entry.ppa_sector') }}" wire:model="entry.ppa_sector">
                        <option value="">Select...</option>
                        @foreach ($cfg['ppa_sectors'] as $o)<option>{{ $o }}</option>@endforeach
                    </select>
                </div>

                <div class="figma-gpb-col" style="flex:1 1 140px;">
                    <label>GAD Objective <span class="req">*</span></label>
                    <textarea class="gad-input" placeholder="Enter Text..." style="{{ $field('entry.gad_objective') }}" wire:model="entry.gad_objective"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:1 1 140px;">
                    <label>Relevant LGU Program or Project</label>
                    <textarea class="gad-input" placeholder="Enter Text..." style="{{ $field('entry.lgu_program') }}" wire:model="entry.lgu_program"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:0 0 130px;">
                    <label>Activity Category <span class="req">*</span></label>
                    <select class="gad-select" style="{{ $field('entry.activity_category') }}" wire:model="entry.activity_category">
                        <option value="">Select...</option>
                        @foreach ($cfg['activity_categories'] as $o)<option>{{ $o }}</option>@endforeach
                    </select>
                </div>

                <div class="figma-gpb-col" style="flex:1 1 130px;">
                    <label>Other Activity Category Value</label>
                    <input class="gad-input" placeholder="Enter Text..." wire:model="entry.other_activity_category">
                </div>
            </div>

        {{-- Row 2: remaining fields --}}
        <div class="figma-gpb-form-row mt-2">
                <div class="figma-gpb-col" style="flex:1 1 160px;">
                    <label>GAD Activity <span class="req">*</span></label>
                    <textarea class="gad-input" placeholder="Enter Text..." style="{{ $field('entry.gad_activity') }}" wire:model="entry.gad_activity"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:0 0 160px;">
                    <label>Date of Implementation <span class="req">*</span></label>
                    <input type="date" class="gad-input" style="{{ $field('entry.date_of_implementation') }}" wire:model="entry.date_of_implementation">
                </div>

                <div class="figma-gpb-col" style="flex:1 1 220px;">
                    <label>Performance Target / Indicator <span class="req">*</span></label>
                    <textarea class="gad-input" placeholder="Enter Text..." style="{{ $field('entry.performance_indicator') }}" wire:model="entry.performance_indicator"></textarea>
                </div>

                <div class="figma-gpb-col" style="flex:0 0 240px;">
                    <label>GAD Budget (MOE, PS, CO) <span class="req">*</span></label>
                    <div class="d-flex gap-2">
                        <div class="flex-fill">
                            <small class="text-muted d-block" style="font-size:10px">MOE</small>
                            <input type="number" step="0.01" min="0" class="gad-input" placeholder="MOE" wire:model="entry.moe">
                        </div>
                        <div class="flex-fill">
                            <small class="text-muted d-block" style="font-size:10px">PS</small>
                            <input type="number" step="0.01" min="0" class="gad-input" placeholder="PS" wire:model="entry.ps">
                        </div>
                        <div class="flex-fill">
                            <small class="text-muted d-block" style="font-size:10px">CO</small>
                            <input type="number" step="0.01" min="0" class="gad-input" placeholder="CO" wire:model="entry.co">
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->hasAny(array_map(fn($k) => "entry.$k", array_keys($entry))))
            <div class="error-text mt-2">Please complete the required fields marked in red (and check number/date formats).</div>
            @endif

            <div class="d-flex justify-content-end gap-2 mt-3 pt-2 border-top">
                <button class="btn-xs clear lg" wire:click="clearEntry"><i class="bi bi-x-circle"></i> Clear</button>
                <button class="btn-xs save lg" wire:click="saveEntry" wire:loading.attr="disabled"><i class="bi bi-floppy"></i> {{ $entryId ? 'Update' : 'Save' }}</button>
            </div>
        </div>
        @endif
        @endif

        {{-- ATTRIBUTED PROGRAM FORM --}}
        @if ($this->canEdit())
        <button type="button" class="form-bar attributed mt-1 {{ $openForm === 'attributed' ? 'open' : '' }}" wire:click="toggleForm('attributed')">
            <span>Attributed Program Input Form</span>
            <i class="bi {{ $openForm === 'attributed' ? 'bi-dash-lg' : 'bi-plus-lg' }}"></i>
        </button>
        @if ($openForm === 'attributed')
        <div class="form-panel form-grid">
            <div class="row g-2">
                <div class="col-2"><label>PPA Sectors <span class="req">*</span></label>
                    <select class="gad-select" style="{{ $field('attr.ppa_sector') }}" wire:model="attr.ppa_sector">
                        <option value="">Select...</option>
                        @foreach ($cfg['ppa_sectors'] as $o)<option>{{ $o }}</option>@endforeach
                    </select></div>
                <div class="col-4"><label>Title of LGU Program or Project <span class="req">*</span></label>
                    <input class="gad-input" style="{{ $field('attr.title') }}" placeholder="Enter Text..." wire:model="attr.title"></div>
                <div class="col-2"><label>Checklist <span class="req">*</span></label>
                    <select class="gad-select" style="{{ $field('attr.checklist') }}" wire:model="attr.checklist">
                        <option value="">Select...</option>
                        @foreach ($cfg['checklists'] as $o)<option>{{ $o }}</option>@endforeach
                    </select></div>
                <div class="col-2"><label>HGDG Design/ Funding Facility Generic Checklist Score</label>
                    <input type="number" step="0.01" min="0" max="20" class="gad-input" style="{{ $field('attr.hgdg_score') }}" wire:model="attr.hgdg_score"></div>
                <div class="col-2"><label>Total Annual Program/ Project Budget</label>
                    <input type="number" step="0.01" min="0" class="gad-input" style="{{ $field('attr.total_annual_budget') }}" wire:model="attr.total_annual_budget"></div>
            </div>
            @if ($errors->hasAny(array_map(fn($k) => "attr.$k", array_keys($attr))))
            <div class="error-text mt-1">Please complete the required fields (HGDG score is 0-20).</div>
            @endif
            <div class="text-end mt-1">
                <button class="btn-xs clear lg" wire:click="clearAttr"><i class="bi bi-x-circle"></i> Clear</button>
                <button class="btn-xs save lg" wire:click="saveAttr" wire:loading.attr="disabled"><i class="bi bi-floppy"></i> {{ $attrId ? 'Update' : 'Save' }}</button>
            </div>
        </div>
        @endif
        @endif

        {{-- CATEGORY FILTER --}}
        @if ($openForm)
        <div class="my-3">
            <span class="gad-label" style="width:100px">Category</span>
            <select class="gad-select" style="width:190px" wire:model.live="categoryFilter">
                <option value="">All</option>
                @foreach (($openForm === 'org' ? $cfg['focused'] : $cfg['ppa_sectors']) as $o)<option>{{ $o }}</option>@endforeach
            </select>
        </div>
        @endif

        {{-- ORG TABLE --}}
        @if ($openForm === 'org')
        <div class="gad-table-wrapper">
        <table class="gad-table">
            <thead>
                <tr>
                    <th rowspan="2">Gender Issue or<br>GAD Mandate</th>
                    <th rowspan="2">GAD Objective</th>
                    <th rowspan="2">Relevant LGU<br>Program or Project</th>
                    <th rowspan="2">GAD Activity</th>
                    <th rowspan="2">Performance<br>Indicator and Target</th>
                    <th colspan="3">GAD Budget</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <th>MOOE</th>
                    <th>PS</th>
                    <th>CO</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $e)
                @php
                    $rGender = $e->remarkFor('gender_issue');
                    $rObj = $e->remarkFor('gad_objective');
                    $rLgu = $e->remarkFor('lgu_program');
                    $rAct = $e->remarkFor('gad_activity');
                    $rTarget = $e->remarkFor('performance_indicator');
                    $rMoe = $e->remarkFor('moe');
                    $rPs = $e->remarkFor('ps');
                    $rCo = $e->remarkFor('co');
                    $isAdmin = (int) auth()->user()->user_role_id === 1;
                @endphp
                <tr wire:key="entry-{{ $e->id }}">
                    {{-- Gender Issue or GAD Mandate --}}
                    <td class="cream position-relative">
                        <div>{{ $e->gender_issue }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            @if ($rGender)
                                <span class="field-remark-preview"><i class="bi bi-chat-quote-fill me-1"></i>{{ Str::limit($rGender, 50) }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($isAdmin || $rGender)
                            <button type="button" class="btn-field-remark {{ $rGender ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'gender_issue', 'Gender Issue or GAD Mandate')">
                                <i class="bi {{ $rGender ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    {{-- GAD Objective --}}
                    <td class="position-relative">
                        <div>{{ $e->gad_objective }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            @if ($rObj)
                                <span class="field-remark-preview"><i class="bi bi-chat-quote-fill me-1"></i>{{ Str::limit($rObj, 50) }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($isAdmin || $rObj)
                            <button type="button" class="btn-field-remark {{ $rObj ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'gad_objective', 'GAD Objective')">
                                <i class="bi {{ $rObj ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    {{-- Relevant LGU Program or Project --}}
                    <td class="position-relative">
                        <div>{{ $e->lgu_program }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            @if ($rLgu)
                                <span class="field-remark-preview"><i class="bi bi-chat-quote-fill me-1"></i>{{ Str::limit($rLgu, 50) }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($isAdmin || $rLgu)
                            <button type="button" class="btn-field-remark {{ $rLgu ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'lgu_program', 'Relevant LGU Program or Project')">
                                <i class="bi {{ $rLgu ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    {{-- GAD Activity --}}
                    <td class="cream position-relative">
                        <div>{{ $e->gad_activity }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            @if ($rAct)
                                <span class="field-remark-preview"><i class="bi bi-chat-quote-fill me-1"></i>{{ Str::limit($rAct, 50) }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($isAdmin || $rAct)
                            <button type="button" class="btn-field-remark {{ $rAct ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'gad_activity', 'GAD Activity')">
                                <i class="bi {{ $rAct ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    {{-- Performance Indicator and Target --}}
                    <td class="position-relative">
                        <div>{{ $e->performance_indicator }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            @if ($rTarget)
                                <span class="field-remark-preview"><i class="bi bi-chat-quote-fill me-1"></i>{{ Str::limit($rTarget, 50) }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($isAdmin || $rTarget)
                            <button type="button" class="btn-field-remark {{ $rTarget ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'performance_indicator', 'Performance Indicator and Target')">
                                <i class="bi {{ $rTarget ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    {{-- MOOE --}}
                    <td class="text-end position-relative">
                        <div>{{ number_format($e->moe, 2) }}</div>
                        <div class="d-flex justify-content-end align-items-center mt-1">
                            @if ($isAdmin || $rMoe)
                            <button type="button" class="btn-field-remark {{ $rMoe ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'moe', 'GAD Budget: MOOE')">
                                <i class="bi {{ $rMoe ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    {{-- PS --}}
                    <td class="text-end position-relative">
                        <div>{{ number_format($e->ps, 2) }}</div>
                        <div class="d-flex justify-content-end align-items-center mt-1">
                            @if ($isAdmin || $rPs)
                            <button type="button" class="btn-field-remark {{ $rPs ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'ps', 'GAD Budget: PS')">
                                <i class="bi {{ $rPs ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    {{-- CO --}}
                    <td class="text-end position-relative">
                        <div>{{ number_format($e->co, 2) }}</div>
                        <div class="d-flex justify-content-end align-items-center mt-1">
                            @if ($isAdmin || $rCo)
                            <button type="button" class="btn-field-remark {{ $rCo ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('entry', {{ $e->id }}, 'co', 'GAD Budget: CO')">
                                <i class="bi {{ $rCo ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>

                    <td style="width:80px">
                        <div class="d-grid gap-1">
                            <button class="btn-xs details" wire:click="showDetails({{ $e->id }})"><i class="bi bi-info-circle-fill"></i>Details</button>
                            @if ($this->canEdit())
                            <button class="btn-xs update" wire:click="editEntry({{ $e->id }})"><i class="bi bi-pencil-fill"></i>Update</button>
                            <button class="btn-xs attach" wire:click="openAttach('entry', {{ $e->id }})"><i class="bi bi-paperclip"></i>Attach</button>
                            <button class="btn-xs delete" wire:click="confirmDelete('entry', {{ $e->id }})"><i class="bi bi-trash-fill"></i>Delete</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-3">No entries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @endif

        {{-- ATTRIBUTED TABLE --}}
        @if ($openForm === 'attributed')
        <div class="gad-table-wrapper">
        <table class="gad-table">
            <thead>
                <tr>
                    <th>Title of LGU Program<br>or Project</th>
                    <th>HGDG Design/ Funding Facility/<br>Generic Checklist Score</th>
                    <th>Total Annual Program/<br>Project Budget</th>
                    <th>GAD Attributed<br>Program/Project Budget</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attributed as $a)
                @php
                    $rTitle = $a->remarkFor('title');
                    $rBudget = $a->remarkFor('total_annual_budget');
                    $isAdmin = (int) auth()->user()->user_role_id === 1;
                @endphp
                <tr wire:key="attr-{{ $a->id }}">
                    <td class="cream position-relative">
                        <div>{{ $a->title }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            @if ($rTitle)
                                <span class="field-remark-preview"><i class="bi bi-chat-quote-fill me-1"></i>{{ Str::limit($rTitle, 50) }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($isAdmin || $rTitle)
                            <button type="button" class="btn-field-remark {{ $rTitle ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('attributed', {{ $a->id }}, 'title', 'Title of LGU Program or Project')">
                                <i class="bi {{ $rTitle ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                    <td>{{ rtrim(rtrim(number_format($a->hgdg_score, 2), '0'), '.') }}</td>
                    <td class="cream text-end position-relative">
                        <div>{{ number_format($a->total_annual_budget, 2) }}</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            @if ($rBudget)
                                <span class="field-remark-preview"><i class="bi bi-chat-quote-fill me-1"></i>{{ Str::limit($rBudget, 40) }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($isAdmin || $rBudget)
                            <button type="button" class="btn-field-remark {{ $rBudget ? 'has-remark' : 'no-remark' }}"
                                title="{{ $isAdmin ? 'Add/Edit Remark' : 'View Remark' }}"
                                wire:click="openFieldRemark('attributed', {{ $a->id }}, 'total_annual_budget', 'Total Annual Program/Project Budget')">
                                <i class="bi {{ $rBudget ? 'bi-chat-left-dots-fill' : 'bi-chat-left-dots' }}"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                    <td class="text-end">{{ number_format($a->gad_attributed_budget, 2) }}</td>
                    <td style="width:80px">
                        <div class="d-grid gap-1">
                            @if ($this->canEdit())
                            <button class="btn-xs update" wire:click="editAttr({{ $a->id }})"><i class="bi bi-pencil-fill"></i>Update</button>
                            <button class="btn-xs attach" wire:click="openAttach('attributed', {{ $a->id }})"><i class="bi bi-paperclip"></i>Attach</button>
                            <button class="btn-xs delete" wire:click="confirmDelete('attributed', {{ $a->id }})"><i class="bi bi-trash-fill"></i>Delete</button>
                            @else
                            <span class="text-muted fst-italic" style="font-size:10px;">Locked</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No attributed programs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @endif
    </div>

    {{-- Details modal --}}
    @if ($detailEntry)
    <div class="gad-modal-backdrop">
        <div class="gad-modal wide">
            <div class="gad-modal-head">DETAILS <button class="x" wire:click="$set('detailsId', null)">&times;</button></div>
            <div class="gad-modal-body">
                <table class="gad-table">
                    @foreach ([
                    'Focused' => $detailEntry->focused,
                    'Gender Issue or GAD Mandate' => $detailEntry->gender_issue,
                    'Title/Description' => $detailEntry->title_description,
                    'Supporting Statistics Data' => $detailEntry->supporting_statistics,
                    'Source of Statistics Data' => $detailEntry->statistics_source,
                    'PPA Sector' => $detailEntry->ppa_sector,
                    'GAD Objective' => $detailEntry->gad_objective,
                    'Relevant LGU Program or Project' => $detailEntry->lgu_program,
                    'Activity Category' => $detailEntry->activity_category . ($detailEntry->other_activity_category ? ' - ' . $detailEntry->other_activity_category : ''),
                    'GAD Activity' => $detailEntry->gad_activity,
                    'Date of Implementation' => optional($detailEntry->date_of_implementation)->format('F j, Y'),
                    'Performance Target / Indicator' => $detailEntry->performance_indicator,
                    'MOOE / PS / CO' => number_format($detailEntry->moe, 2) . ' / ' . number_format($detailEntry->ps, 2) . ' / ' . number_format($detailEntry->co, 2),
                    ] as $label => $value)
                    <tr><td class="fw-bold" style="width:32%;background:#f6f9ff">{{ $label }}</td><td>{{ $value ?: '-' }}</td></tr>
                    @endforeach
                    @if ($detailEntry->attachment)
                    <tr><td class="fw-bold" style="background:#f6f9ff">Attachment</td><td><a target="_blank" href="{{ asset('storage/' . $detailEntry->attachment) }}">View attachment</a></td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete modal --}}
    @if ($deleteId)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">DELETE <button class="x" wire:click="$set('deleteId', null)">&times;</button></div>
            <div class="gad-modal-body text-center">
                <p>Are you sure you want to delete this record?</p>
                <button class="btn-xs lg" style="background:#6c757d" wire:click="$set('deleteId', null)">Cancel</button>
                <button class="btn-xs delete lg" wire:click="delete">Delete</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Attach modal (row) --}}
    @if ($attachId)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">ATTACH FILE <button class="x" wire:click="$set('attachId', null)">&times;</button></div>
            <div class="gad-modal-body">
                <input type="file" class="form-control form-control-sm mb-1" wire:model="file">
                @error('file') <div class="error-text">{{ $message }}</div> @enderror
                <div wire:loading wire:target="file" class="error-text">Uploading...</div>
                <div class="text-end mt-3">
                    <button class="btn-xs save lg" wire:click="saveAttach" wire:loading.attr="disabled" wire:target="saveAttach,file"><i class="bi bi-floppy"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Attach AR modal --}}
    @if ($showAr)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">ATTACH AR <button class="x" wire:click="$set('showAr', false)">&times;</button></div>
            <div class="gad-modal-body">
                <input type="file" class="form-control form-control-sm mb-1" wire:model="arFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                @error('arFile') <div class="error-text">{{ $message }}</div> @enderror
                <div wire:loading wire:target="arFile" class="error-text">Uploading...</div>
                <div class="text-end mt-3">
                    <button class="btn-xs save lg" wire:click="saveAr" wire:loading.attr="disabled" wire:target="saveAr,arFile"><i class="bi bi-floppy"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Field Remark Modal (Admin can add/edit/delete, User can view) --}}
    @if ($remarkModal)
    <div class="gad-modal-backdrop">
        <div class="gad-modal">
            <div class="gad-modal-head">
                <i class="bi bi-chat-left-dots-fill me-1 text-warning"></i>
                REMARK: {{ $remarkFieldLabel }}
                <button class="x" wire:click="$set('remarkModal', false)">&times;</button>
            </div>
            <div class="gad-modal-body">
                @if ((int) auth()->user()->user_role_id === 1)
                    <label class="gad-label mb-2 w-100">Reviewer Remark (Admin)</label>
                    <textarea class="gad-input w-100 mb-2" rows="4" placeholder="Enter comments or correction needed for this field..." wire:model="remarkText"></textarea>
                    @error('remarkText') <div class="error-text mb-2">{{ $message }}</div> @enderror

                    @if ($currentRemark)
                    <div class="text-muted mb-3" style="font-size: 11px;">
                        <i class="bi bi-clock me-1"></i>Last updated by {{ $currentRemark->admin->name ?? 'Admin' }} on {{ $currentRemark->updated_at->format('M d, Y g:i A') }}
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        @if ($currentRemark)
                        <button type="button" class="btn-xs delete lg" wire:click="deleteFieldRemark" wire:confirm="Are you sure you want to delete this remark?">
                            <i class="bi bi-trash"></i> Delete Remark
                        </button>
                        @else
                        <span></span>
                        @endif
                        <div class="d-flex gap-2">
                            <button type="button" class="btn-xs lg" style="background:#6c757d" wire:click="$set('remarkModal', false)">Cancel</button>
                            <button type="button" class="btn-xs save lg" wire:click="saveFieldRemark"><i class="bi bi-floppy"></i> Save Remark</button>
                        </div>
                    </div>
                @else
                    {{-- User View Mode --}}
                    @if ($currentRemark && $currentRemark->remarks)
                        <div class="p-3 mb-3" style="background:#fffbeb; border:1px solid #fef3c7; border-radius:8px;">
                            <div class="d-flex align-items-center gap-2 mb-2 text-warning-emphasis fw-bold">
                                <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                <span>Note from Reviewer / Admin:</span>
                            </div>
                            <div style="font-size: 12.5px; line-height: 1.5; color:#78350f; white-space: pre-wrap;">{{ $currentRemark->remarks }}</div>
                            <div class="mt-2 text-muted" style="font-size: 10.5px;">
                                Updated {{ $currentRemark->updated_at->diffForHumans() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-chat-left-dots fs-3 d-block mb-1"></i>
                            No remarks left on this field yet.
                        </div>
                    @endif
                    <div class="text-end mt-3">
                        <button type="button" class="btn-xs lg" style="background:#6c757d" wire:click="$set('remarkModal', false)">Close</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

