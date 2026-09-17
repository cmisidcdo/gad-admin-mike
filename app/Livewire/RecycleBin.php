<?php

namespace App\Livewire;

use App\Models\TblDocumentData;
use Livewire\Component;
use Livewire\WithPagination;

class RecycleBin extends Component
{
    use WithPagination;

    public $search = '';

    public $rModal = 0;
    // ^^^ Confirmation Message Modal

    public $Posttrans_id;
    public $trans_id; // Hidden input field in the confirmation box.

    public function render()
    {
        $data = TblDocumentData::search($this->search)
            ->where('is_active', 0)
            ->paginate(10);

        return view('livewire.recycle-bin', [
            'data'         => $data,
            'currentPage'  => $data->currentPage(),
            'totalPages'   => $data->lastPage(),
            'totalRecords' => $data->total(),
            'noRecords'    => $data->isEmpty(),
        ]);
    }

    public function updating()
    {
        $this->resetPage();
    }

    // We get the id and store it pass it to $trans_id and store it to $this->Postrans_id which will be available to the blade.
    public function restore($trans_id)
    {
        $item = TblDocumentData::findOrFail($trans_id);
        $this->Posttrans_id     =      $trans_id;
        // $this->trans_id         =      $item->trans_id;

        $this->openRestoreModal();
    }

    // Confirmation Message
    public function RestoreModal()
    {
        $this->openRestoreModal();
    }
    public function openRestoreModal()
    {
        $this->rModal = true;
    }
    public function closeRestoreModal()
    {
        $this->rModal = false;
    }

    // Restore: Update `is_active` to 1(Active).
    public function updateRestore()
    {
        if ($this->Posttrans_id) {
            $item = TblDocumentData::findOrFail($this->Posttrans_id);
            $item->update([
                'is_active'     =>       1,
            ]);

            session()->flash('status', 'Restored successfully.');
            $this->closeRestoreModal();
            $this->reset('Posttrans_id'); // We need to reset this property so that when user clicks another row, the actual `id` will be stored there.
        }
    }
}
