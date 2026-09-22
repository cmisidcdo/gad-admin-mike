<?php

namespace App\Livewire;

use App\Mail\SubmissionReminderMail;
use App\Models\TblDocumentInformation;
use App\Models\TblRefDepartment;
use App\Models\TblRefSector;
use App\Models\TblRefSubSector;
use App\Models\TblUserSectorPermission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class NotYetSubmitted extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $selectedYear = '';
    public $selectedSemester = '';
    public $selectedSector = '';
    public $selectedSubSector = '';
    public $selectedDepartment = '';
    public $errorMessage = '';

    // Selection State
    public $selectedUserIds = [];
    public $selectAll = false;

    // Email Modal & Sending State
    public $showEmailModal = false;
    public $emailMode = 'all'; // 'all', 'selected', or 'single'
    public $emailSubject = 'Reminder: Pending GAD Document Submission';
    public $emailCustomMessage = '';
    public $singleUserId = null;
    public $singleUserName = '';
    public $singleUserEmail = '';
    public $singleUserDepartment = '';
    public $singleUserUnsubmittedSectors = [];
    public $modalRecipientsCount = 0;
    public $modalValidEmailCount = 0;
    public $modalMissingEmailCount = 0;

    public function mount()
    {
        // Enforce Admin only
        if ((int) auth()->user()->user_role_id !== 1) {
            abort(403, 'Unauthorized');
        }

        // Default to current year and 1st semester
        $this->selectedYear     = (string) Carbon::now()->year;
        $this->selectedSemester = '1';
        $this->selectedSector   = '';
        $this->selectedSubSector = '';
    }

    public function updatedSelectedYear()
    {
        $this->selectedUserIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectedSemester()
    {
        $this->selectedUserIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectedSector()
    {
        $this->selectedSubSector = '';
        $this->selectedUserIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectedSubSector()
    {
        $this->selectedUserIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->selectedUserIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectedDepartment()
    {
        $this->selectedUserIds = [];
        $this->selectAll = false;
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUserIds = $this->getUnsubmittedUsersQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedUserIds = [];
        }
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedDepartment = '';
        $this->selectedYear     = (string) Carbon::now()->year;
        $this->selectedSemester = '1';
        $this->selectedSector   = '';
        $this->selectedSubSector = '';
        $this->selectedUserIds  = [];
        $this->selectAll        = false;
        $this->resetPage();
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
     * Get sectors accessible to admin.
     */
    protected function getSectors()
    {
        $sectorPermissions = TblUserSectorPermission::where('id_user', auth()->id())->get();
        if ($sectorPermissions->isNotEmpty()) {
            $allowedSectorIds = $sectorPermissions->pluck('sector_id')->unique()->toArray();
            return TblRefSector::whereIn('sector_id', $allowedSectorIds)->orderBy('sort_order')->get();
        }
        return TblRefSector::orderBy('sort_order')->get();
    }

    /**
     * Get sub-sectors for a selected sector.
     */
    protected function getSubSectors($sectorId)
    {
        if (empty($sectorId)) {
            return collect();
        }

        $sectorPermissions = TblUserSectorPermission::where('id_user', auth()->id())->get();
        if ($sectorPermissions->isNotEmpty()) {
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

        return TblRefSubSector::where('sector_id', $sectorId)->orderBy('sort_order')->get();
    }

    /**
     * Helper to return human-friendly label of current reporting period.
     */
    public function getPeriodLabel(): string
    {
        $parts = [];

        if (!empty($this->selectedYear)) {
            $yearLabel = 'Year ' . $this->selectedYear;
            if ($this->selectedSemester === '1') {
                $parts[] = "{$yearLabel} — 1st Semester (Jan - Jun)";
            } elseif ($this->selectedSemester === '2') {
                $parts[] = "{$yearLabel} — 2nd Semester (Jul - Dec)";
            } else {
                $parts[] = "{$yearLabel} (Full Year)";
            }
        } elseif (!empty($this->selectedSemester)) {
            $parts[] = $this->selectedSemester === '1' ? '1st Semester (Jan - Jun)' : '2nd Semester (Jul - Dec)';
        } else {
            $parts[] = 'All Years';
        }

        if (!empty($this->selectedSector)) {
            $sector = TblRefSector::where('sector_id', $this->selectedSector)->first();
            if ($sector) {
                $parts[] = $sector->sector_name;
            }
        }

        if (!empty($this->selectedSubSector)) {
            $subSector = TblRefSubSector::where('sub_sector_id', $this->selectedSubSector)->first();
            if ($subSector) {
                $parts[] = $subSector->sub_sector_name;
            }
        }

        return implode(' | ', $parts);
    }

    /**
     * Apply date/period filtering to a document query.
     */
    protected function applyDateRangeToQuery($query, string $dateColumn = 'created_at', ?string $legacyDateColumn = 'create_date')
    {
        if (!empty($this->selectedYear)) {
            $year = (int) $this->selectedYear;
            if ($this->selectedSemester === '1') {
                $from = Carbon::createFromDate($year, 1, 1)->startOfDay()->format('Y-m-d H:i:s');
                $to   = Carbon::createFromDate($year, 6, 30)->endOfDay()->format('Y-m-d H:i:s');
            } elseif ($this->selectedSemester === '2') {
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
        } elseif (!empty($this->selectedSemester)) {
            if ($this->selectedSemester === '1') {
                $query->where(function ($q) use ($dateColumn, $legacyDateColumn) {
                    $q->whereRaw("MONTH({$dateColumn}) BETWEEN 1 AND 6");
                    if ($legacyDateColumn) {
                        $q->orWhereRaw("MONTH(STR_TO_DATE({$legacyDateColumn}, '%c/%e/%Y %r')) BETWEEN 1 AND 6");
                    }
                });
            } elseif ($this->selectedSemester === '2') {
                $query->where(function ($q) use ($dateColumn, $legacyDateColumn) {
                    $q->whereRaw("MONTH({$dateColumn}) BETWEEN 7 AND 12");
                    if ($legacyDateColumn) {
                        $q->orWhereRaw("MONTH(STR_TO_DATE({$legacyDateColumn}, '%c/%e/%Y %r')) BETWEEN 7 AND 12");
                    }
                });
            }
        }
    }

    /**
     * Build the query for active users with 'User' role (user_role_id = 4) who have unsubmitted assigned sectors/subsectors.
     */
    public function getUnsubmittedUsersQuery()
    {
        $usersQuery = User::with(['department', 'role', 'sectorPermissions.sector', 'sectorPermissions.subSector'])
            ->where('is_active', 'Y')
            ->where('user_role_id', 4);

        $selectedSector = $this->selectedSector;
        $selectedSubSector = $this->selectedSubSector;

        $usersQuery->where(function ($query) use ($selectedSector, $selectedSubSector) {
            // 1. Users with sector permissions who have at least one unsubmitted assignment
            $query->whereHas('sectorPermissions', function ($pQuery) use ($selectedSector, $selectedSubSector) {
                if (!empty($selectedSector)) {
                    $pQuery->where('sector_id', (string) $selectedSector);
                }
                if (!empty($selectedSubSector)) {
                    $pQuery->where(function ($subQ) use ($selectedSubSector) {
                        $subQ->whereNull('sub_sector_id')
                             ->orWhere('sub_sector_id', (string) $selectedSubSector);
                    });
                }

                // Check that NO document was submitted for this permission in the period
                $pQuery->whereNotExists(function ($dQuery) use ($selectedSubSector) {
                    $dQuery->select(DB::raw(1))
                        ->from('tbl_document_information as d')
                        ->where(function ($uMatch) {
                            $uMatch->whereColumn('d.create_user', 'users.name')
                                   ->orWhereColumn('d.create_user', 'users.user_id');
                        })
                        ->whereColumn('d.sector', 'tbl_user_sector_permission.sector_id')
                        ->where(function ($subMatch) use ($selectedSubSector) {
                            if (!empty($selectedSubSector)) {
                                $subMatch->where('d.sub_sector', (string) $selectedSubSector);
                            } else {
                                $subMatch->whereNull('tbl_user_sector_permission.sub_sector_id')
                                         ->orWhereColumn('d.sub_sector', 'tbl_user_sector_permission.sub_sector_id');
                            }
                        });

                    $this->applyDateRangeToQuery($dQuery, 'd.created_at', 'd.create_date');
                });
            });

            // 2. When NO sector filter is active, also include users who have NO assigned sector permissions and have 0 submitted documents
            if (empty($selectedSector)) {
                $query->orWhere(function ($noPermQuery) {
                    $noPermQuery->whereDoesntHave('sectorPermissions')
                        ->whereNotExists(function ($dQuery) {
                            $dQuery->select(DB::raw(1))
                                ->from('tbl_document_information as d')
                                ->where(function ($uMatch) {
                                    $uMatch->whereColumn('d.create_user', 'users.name')
                                           ->orWhereColumn('d.create_user', 'users.user_id');
                                });

                            $this->applyDateRangeToQuery($dQuery, 'd.created_at', 'd.create_date');
                        });
                });
            }
        });

        // Apply department filter
        if (!empty($this->selectedDepartment)) {
            $usersQuery->where('office', $this->selectedDepartment);
        }

        // Apply search filter
        if (!empty($this->search)) {
            $search = $this->search;
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('user_id', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('department', function ($deptQ) use ($search) {
                      $deptQ->where('department_name', 'like', '%' . $search . '%');
                  });
            });
        }

        return $usersQuery;
    }

    /**
     * Compute unsubmitted sectors and subsectors for a given user.
     */
    public function calculateUnsubmittedSectorsForUser($user, $userDocs, $allSectorsMap, $allSubSectorsMap): array
    {
        $perms = $user->sectorPermissions;

        // Apply active sector/subsector filter if selected
        if (!empty($this->selectedSector)) {
            $perms = $perms->where('sector_id', (string) $this->selectedSector);
            if (!empty($this->selectedSubSector)) {
                $perms = $perms->filter(function ($p) {
                    return $p->sub_sector_id === null || (string) $p->sub_sector_id === (string) $this->selectedSubSector;
                });
            }
        }

        $unsubmitted = [];

        if ($perms->isEmpty()) {
            if ($userDocs->isEmpty()) {
                $unsubmitted[] = [
                    'sector_id' => null,
                    'sector_name' => 'Unassigned',
                    'sub_sector_id' => null,
                    'sub_sector_name' => null,
                    'display' => 'No assigned sectors',
                    'is_unassigned' => true,
                ];
            }
        } else {
            foreach ($perms as $perm) {
                $sec = $allSectorsMap->get($perm->sector_id);
                $secName = $sec ? $sec->sector_name : "Sector {$perm->sector_id}";

                if ($perm->sub_sector_id !== null) {
                    $subSecList = $allSubSectorsMap->get($perm->sector_id, collect());
                    $sub = $subSecList->firstWhere('sub_sector_id', $perm->sub_sector_id);
                    $subName = $sub ? $sub->sub_sector_name : "Subsector {$perm->sub_sector_id}";

                    $hasSubmitted = $userDocs->where('sector', $perm->sector_id)
                        ->where('sub_sector', $perm->sub_sector_id)
                        ->isNotEmpty();

                    if (!$hasSubmitted) {
                        $unsubmitted[] = [
                            'sector_id' => $perm->sector_id,
                            'sector_name' => $secName,
                            'sub_sector_id' => $perm->sub_sector_id,
                            'sub_sector_name' => $subName,
                            'display' => "{$secName}: {$subName}",
                            'is_unassigned' => false,
                        ];
                    }
                } else {
                    // All subsectors for this sector
                    $subSecList = $allSubSectorsMap->get($perm->sector_id, collect());
                    if (!empty($this->selectedSubSector)) {
                        $subSecList = $subSecList->where('sub_sector_id', (string) $this->selectedSubSector);
                    }

                    if ($subSecList->isEmpty()) {
                        $hasSubmitted = $userDocs->where('sector', $perm->sector_id)->isNotEmpty();
                        if (!$hasSubmitted) {
                            $unsubmitted[] = [
                                'sector_id' => $perm->sector_id,
                                'sector_name' => $secName,
                                'sub_sector_id' => null,
                                'sub_sector_name' => null,
                                'display' => $secName,
                                'is_unassigned' => false,
                            ];
                        }
                    } else {
                        $unsubmittedSubs = [];
                        foreach ($subSecList as $sub) {
                            $hasSubmitted = $userDocs->where('sector', $perm->sector_id)
                                ->where('sub_sector', $sub->sub_sector_id)
                                ->isNotEmpty();
                            if (!$hasSubmitted) {
                                $unsubmittedSubs[] = $sub;
                            }
                        }

                        if (count($unsubmittedSubs) === $subSecList->count() && empty($this->selectedSubSector)) {
                            $unsubmitted[] = [
                                'sector_id' => $perm->sector_id,
                                'sector_name' => $secName,
                                'sub_sector_id' => null,
                                'sub_sector_name' => 'All Subsectors',
                                'display' => "{$secName} (All Subsectors)",
                                'is_unassigned' => false,
                            ];
                        } else {
                            foreach ($unsubmittedSubs as $sub) {
                                $unsubmitted[] = [
                                    'sector_id' => $perm->sector_id,
                                    'sector_name' => $secName,
                                    'sub_sector_id' => $sub->sub_sector_id,
                                    'sub_sector_name' => $sub->sub_sector_name,
                                    'display' => "{$secName}: {$sub->sub_sector_name}",
                                    'is_unassigned' => false,
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $unsubmitted;
    }

    /**
     * Open confirmation modal for sending reminder emails to ALL currently unsubmitted users.
     */
    public function openBatchEmailModal()
    {
        $this->emailMode = 'all';
        $this->emailSubject = 'Reminder: Pending GAD Document Submission - ' . $this->getPeriodLabel();
        $this->emailCustomMessage = '';

        $unsubmittedUsers = $this->getUnsubmittedUsersQuery()->get();
        $this->modalRecipientsCount = $unsubmittedUsers->count();
        $this->modalValidEmailCount = $unsubmittedUsers->filter(function ($u) {
            return !empty($u->email) && filter_var($u->email, FILTER_VALIDATE_EMAIL);
        })->count();
        $this->modalMissingEmailCount = $this->modalRecipientsCount - $this->modalValidEmailCount;

        $this->showEmailModal = true;
    }

    /**
     * Open confirmation modal for sending reminder emails to SELECTED checked users.
     */
    public function openSelectedEmailModal()
    {
        if (empty($this->selectedUserIds)) {
            session()->flash('error', 'Please select at least one user from the list.');
            return;
        }

        $this->emailMode = 'selected';
        $this->emailSubject = 'Reminder: Pending GAD Document Submission - ' . $this->getPeriodLabel();
        $this->emailCustomMessage = '';

        $selectedUsers = User::with('department')->whereIn('id', $this->selectedUserIds)->get();
        $this->modalRecipientsCount = $selectedUsers->count();
        $this->modalValidEmailCount = $selectedUsers->filter(function ($u) {
            return !empty($u->email) && filter_var($u->email, FILTER_VALIDATE_EMAIL);
        })->count();
        $this->modalMissingEmailCount = $this->modalRecipientsCount - $this->modalValidEmailCount;

        $this->showEmailModal = true;
    }

    /**
     * Open confirmation modal for sending reminder email to a single user.
     */
    public function openSingleEmailModal($userId)
    {
        $user = User::with(['department', 'sectorPermissions.sector', 'sectorPermissions.subSector'])->find($userId);

        if (!$user) {
            session()->flash('error', 'Selected user was not found.');
            return;
        }

        $this->emailMode = 'single';
        $this->singleUserId = $user->id;
        $this->singleUserName = $user->name;
        $this->singleUserEmail = $user->email ?? '';
        $this->singleUserDepartment = $user->department->department_name ?? $user->office ?? 'N/A';
        $this->emailSubject = 'Reminder: Pending GAD Document Submission - ' . $this->getPeriodLabel();
        $this->emailCustomMessage = '';

        // Compute unsubmitted sectors for this single user
        $submittedDocsQuery = TblDocumentInformation::where(function ($q) use ($user) {
            $q->where('create_user', $user->name)
              ->orWhere('create_user', $user->user_id);
        });
        $this->applyDateRangeToQuery($submittedDocsQuery, 'created_at', 'create_date');
        $userDocs = $submittedDocsQuery->get();

        $allSectorsMap = TblRefSector::all()->keyBy('sector_id');
        $allSubSectorsMap = TblRefSubSector::all()->groupBy('sector_id');

        $this->singleUserUnsubmittedSectors = $this->calculateUnsubmittedSectorsForUser(
            $user,
            $userDocs,
            $allSectorsMap,
            $allSubSectorsMap
        );

        $this->modalRecipientsCount = 1;
        $hasValidEmail = !empty($user->email) && filter_var($user->email, FILTER_VALIDATE_EMAIL);
        $this->modalValidEmailCount = $hasValidEmail ? 1 : 0;
        $this->modalMissingEmailCount = $hasValidEmail ? 0 : 1;

        $this->showEmailModal = true;
    }

    /**
     * Close modal and reset state.
     */
    public function closeEmailModal()
    {
        $this->showEmailModal = false;
        $this->reset(
            'emailMode',
            'singleUserId',
            'singleUserName',
            'singleUserEmail',
            'singleUserDepartment',
            'singleUserUnsubmittedSectors',
            'emailCustomMessage',
            'modalRecipientsCount',
            'modalValidEmailCount',
            'modalMissingEmailCount'
        );
        $this->resetValidation();
    }

    /**
     * Send email reminders based on active mode (batch, selected, or single).
     */
    public function sendEmails()
    {
        $this->validate([
            'emailSubject'       => 'required|string|max:200',
            'emailCustomMessage' => 'nullable|string|max:1500',
        ], [
            'emailSubject.required' => 'Please provide an email subject line.',
        ]);

        $periodLabel = $this->getPeriodLabel();

        // 1. Single Mode
        if ($this->emailMode === 'single') {
            $user = User::with('department')->find($this->singleUserId);

            if (!$user) {
                session()->flash('error', 'User not found.');
                $this->closeEmailModal();
                return;
            }

            if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                session()->flash('error', "Cannot send email: {$user->name} has no valid email address on file.");
                $this->closeEmailModal();
                return;
            }

            try {
                Mail::to($user->email)->send(new SubmissionReminderMail(
                    user: $user,
                    periodLabel: $periodLabel,
                    emailSubject: $this->emailSubject,
                    customMessage: $this->emailCustomMessage,
                    unsubmittedSectors: $this->singleUserUnsubmittedSectors
                ));

                session()->flash('status', "Reminder email was successfully sent to {$user->name} ({$user->email}).");
            } catch (\Exception $e) {
                Log::error("Error sending submission reminder email to user {$user->id} ({$user->email}): " . $e->getMessage());
                session()->flash('error', "Failed to send email to {$user->name}: " . $e->getMessage());
            }

            $this->closeEmailModal();
            return;
        }

        // 2. Selected or All Mode
        if ($this->emailMode === 'selected') {
            $targetUsers = User::with(['department', 'sectorPermissions.sector', 'sectorPermissions.subSector'])->whereIn('id', $this->selectedUserIds)->get();
        } else {
            // 'all' Mode
            $targetUsers = $this->getUnsubmittedUsersQuery()->get();
        }

        if ($targetUsers->isEmpty()) {
            session()->flash('error', 'No recipients found for email dispatch.');
            $this->closeEmailModal();
            return;
        }

        $allSectorsMap = TblRefSector::all()->keyBy('sector_id');
        $allSubSectorsMap = TblRefSubSector::all()->groupBy('sector_id');

        $targetNames = $targetUsers->pluck('name')->filter()->unique()->toArray();
        $targetIds = $targetUsers->pluck('user_id')->filter()->unique()->toArray();
        $allTargetIdentifiers = array_unique(array_merge($targetNames, $targetIds));

        $submittedDocsQuery = TblDocumentInformation::whereIn('create_user', $allTargetIdentifiers);
        $this->applyDateRangeToQuery($submittedDocsQuery, 'created_at', 'create_date');
        $allTargetDocs = $submittedDocsQuery->get();

        $sentCount = 0;
        $skippedCount = 0;
        $failedCount = 0;

        foreach ($targetUsers as $user) {
            if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                continue;
            }

            $userDocs = $allTargetDocs->filter(function ($doc) use ($user) {
                return $doc->create_user === $user->name || $doc->create_user === $user->user_id;
            });

            $unsubmittedSectors = $this->calculateUnsubmittedSectorsForUser(
                $user,
                $userDocs,
                $allSectorsMap,
                $allSubSectorsMap
            );

            try {
                Mail::to($user->email)->send(new SubmissionReminderMail(
                    user: $user,
                    periodLabel: $periodLabel,
                    emailSubject: $this->emailSubject,
                    customMessage: $this->emailCustomMessage,
                    unsubmittedSectors: $unsubmittedSectors
                ));
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to send reminder email to {$user->email}: " . $e->getMessage());
            }
        }

        // Build descriptive feedback message
        $feedbackParts = [];
        if ($sentCount > 0) {
            $feedbackParts[] = "Successfully sent {$sentCount} reminder email" . ($sentCount > 1 ? 's' : '') . '.';
        } else {
            $feedbackParts[] = "No emails were sent.";
        }

        if ($skippedCount > 0) {
            $feedbackParts[] = "{$skippedCount} user" . ($skippedCount > 1 ? 's were' : ' was') . " skipped because they have no email address on file.";
        }

        if ($failedCount > 0) {
            $feedbackParts[] = "{$failedCount} email" . ($failedCount > 1 ? 's' : '') . " failed to deliver due to mail server connection issues.";
        }

        $fullFeedback = implode(' ', $feedbackParts);

        if ($sentCount > 0) {
            session()->flash('status', $fullFeedback);
        } else {
            session()->flash('error', $fullFeedback);
        }

        // Reset selections after send
        $this->selectedUserIds = [];
        $this->selectAll = false;
        $this->closeEmailModal();
    }

    public function render()
    {
        $departments = TblRefDepartment::orderBy('department_name', 'asc')->get();
        $sectors = $this->getSectors();
        $subSectors = $this->getSubSectors($this->selectedSector);
        $availableYears = $this->getAvailableYears();

        // Base query for active users matching current user-level filters (sector permission, office, search)
        $baseUsersQuery = User::where('is_active', 'Y')
            ->where('user_role_id', 4);

        if (!empty($this->selectedSector)) {
            $baseUsersQuery->whereHas('sectorPermissions', function ($sq) {
                $sq->where('sector_id', (string) $this->selectedSector);
                if (!empty($this->selectedSubSector)) {
                    $sq->where(function ($subQ) {
                        $subQ->whereNull('sub_sector_id')
                             ->orWhere('sub_sector_id', (string) $this->selectedSubSector);
                    });
                }
            });
        }

        if (!empty($this->selectedDepartment)) {
            $baseUsersQuery->where('office', $this->selectedDepartment);
        }

        if (!empty($this->search)) {
            $search = $this->search;
            $baseUsersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('user_id', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('department', function ($deptQ) use ($search) {
                      $deptQ->where('department_name', 'like', '%' . $search . '%');
                  });
            });
        }

        $totalActiveUsersCount = (clone $baseUsersQuery)->count();

        // Unsubmitted users query
        $usersQuery = $this->getUnsubmittedUsersQuery();

        $totalNotSubmitted = (clone $usersQuery)->count();
        $totalSubmittedCount = max(0, $totalActiveUsersCount - $totalNotSubmitted);

        $notSubmittedUsers = $usersQuery->orderBy('name', 'asc')->paginate(10);

        // Preload sectors and subsectors for mapping
        $allSectorsMap = $sectors->keyBy('sector_id');
        $allSubSectorsMap = TblRefSubSector::all()->groupBy('sector_id');

        // Batch-fetch submitted documents for current page users in single query
        $pageUserNames = $notSubmittedUsers->pluck('name')->filter()->unique()->toArray();
        $pageUserIds = $notSubmittedUsers->pluck('user_id')->filter()->unique()->toArray();
        $allPageIdentifiers = array_unique(array_merge($pageUserNames, $pageUserIds));

        $submittedDocsQuery = TblDocumentInformation::whereIn('create_user', $allPageIdentifiers);
        $this->applyDateRangeToQuery($submittedDocsQuery, 'created_at', 'create_date');
        $pageSubmittedDocs = $submittedDocsQuery->get();

        foreach ($notSubmittedUsers as $user) {
            $userDocs = $pageSubmittedDocs->filter(function ($doc) use ($user) {
                return $doc->create_user === $user->name || $doc->create_user === $user->user_id;
            });
            $user->unsubmitted_sectors = $this->calculateUnsubmittedSectorsForUser(
                $user,
                $userDocs,
                $allSectorsMap,
                $allSubSectorsMap
            );
        }

        return view('livewire.not-yet-submitted', [
            'departments'                  => $departments,
            'sectors'                      => $sectors,
            'subSectors'                   => $subSectors,
            'availableYears'               => $availableYears,
            'notSubmittedUsers'            => $notSubmittedUsers,
            'totalActiveUsersCount'        => $totalActiveUsersCount,
            'totalSubmittedCount'          => $totalSubmittedCount,
            'totalNotSubmitted'            => $totalNotSubmitted,
            'currentPage'                  => $notSubmittedUsers->currentPage(),
            'totalPages'                   => $notSubmittedUsers->lastPage(),
            'totalRecords'                 => $notSubmittedUsers->total(),
            'noRecords'                    => $notSubmittedUsers->isEmpty(),
            'currentPeriodLabel'           => $this->getPeriodLabel(),
            'selectedUserIds'              => $this->selectedUserIds,
            'selectedYear'                 => $this->selectedYear,
            'selectedSemester'             => $this->selectedSemester,
            'selectedSector'               => $this->selectedSector,
            'selectedSubSector'            => $this->selectedSubSector,
            'selectedDepartment'           => $this->selectedDepartment,
            'search'                       => $this->search,
            'errorMessage'                 => $this->errorMessage,
            'selectAll'                    => $this->selectAll,
            'showEmailModal'               => $this->showEmailModal,
            'emailMode'                    => $this->emailMode,
            'emailSubject'                 => $this->emailSubject,
            'emailCustomMessage'           => $this->emailCustomMessage,
            'singleUserId'                 => $this->singleUserId,
            'singleUserName'               => $this->singleUserName,
            'singleUserEmail'              => $this->singleUserEmail,
            'singleUserDepartment'         => $this->singleUserDepartment,
            'singleUserUnsubmittedSectors' => $this->singleUserUnsubmittedSectors,
            'modalRecipientsCount'         => $this->modalRecipientsCount,
            'modalValidEmailCount'         => $this->modalValidEmailCount,
            'modalMissingEmailCount'       => $this->modalMissingEmailCount,
        ]);
    }
}
