<?php

namespace App\Livewire\Plt;

use App\Exports\HmUpdateExport;
use App\Imports\HmUpdateImport;
use App\Models\Equipment;
use App\Models\EquipmentHm;
use App\Services\HmInterpolationService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class HmUpdatePage extends Component
{
    use WithFileUploads, WithPagination;

    public $searchUnit = '';

    public $searchDate = '';

    // Manual input fields
    public $equipment_id;

    public $date;

    public $hm_value;

    // Bulk upload fields
    public $excel_file;

    protected $listeners = ['refreshHm' => '$refresh'];

    public function mount()
    {
        $this->date = date('Y-m-d'); // Default to today
    }

    public function updatingSearchUnit()
    {
        $this->resetPage();
    }

    public function updatingSearchDate()
    {
        $this->resetPage();
    }

    public function saveManual()
    {
        $this->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'date' => 'required|date|before_or_equal:today',
            'hm_value' => 'required|numeric|min:0',
        ]);

        // Validation rule: New HM must be >= last HM before this date
        $equipment = Equipment::find($this->equipment_id);
        $lastHm = $equipment->getLastHmBeforeDate($this->date);

        if ($this->hm_value < $lastHm) {
            $this->addError('hm_value', 'HM tidak valid! HM terakhir sebelum atau pada tanggal '.Carbon::parse($this->date)->format('d M Y')." adalah {$lastHm}. HM baru harus lebih besar atau sama.");

            return;
        }

        app(HmInterpolationService::class)->recordHm(
            $this->equipment_id,
            $this->date,
            $this->hm_value,
            'manual',
            auth()->id()
        );

        $this->reset(['equipment_id', 'hm_value']);
        $this->date = date('Y-m-d');

        $this->dispatch('close-modal', id: 'manualInputModal');
        session()->flash('success', 'HM berhasil disimpan dan direkalkulasi.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new HmUpdateExport, 'hm_upload_template.xlsx');
    }

    public function uploadExcel()
    {
        $this->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Max 5MB
        ]);

        try {
            Excel::import(new HmUpdateImport, $this->excel_file);

            $this->reset('excel_file');
            $this->dispatch('close-modal', id: 'uploadModal');
            session()->flash('success', 'Data HM dari Excel berhasil diunggah dan diinterpolasi secara otomatis.');
        } catch (\Exception $e) {
            $this->addError('excel_file', 'Gagal memproses file: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = EquipmentHm::with('equipment', 'creator', 'updater')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($this->searchUnit) {
            $query->whereHas('equipment', function ($q) {
                $q->where('unit', 'like', '%'.$this->searchUnit.'%')
                    ->orWhere('no', 'like', '%'.$this->searchUnit.'%');
            });
        }

        if ($this->searchDate) {
            $query->whereDate('date', $this->searchDate);
        }

        $logs = $query->paginate(20);
        $equipments = Equipment::orderBy('unit')->get();

        return view('components.plt.hm-update-page', [
            'logs' => $logs,
            'equipments' => $equipments,
        ])->layout('layouts.user');
    }
}
