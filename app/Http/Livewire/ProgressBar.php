<?php

namespace App\Http\Livewire;

use App\Models\Import;
use Livewire\Component;
use Predis\Command\Redis\DUMP;

class ProgressBar extends Component
{
    public $modelClass;

    protected $listeners = ['showProgressBar' => '$refresh'];

    public function render()
    {
        $progresses = [];
        $info = auth()->user()
            ->imports()
            ->inProgress()
            ->forModel($modelClass = $this->modelClass)
            ->latest()
            ->get();
        $info->each(function ($import) use(&$progresses){
            $percentageProcessedRows = round(($import->processed_rows / $import->total_rows) * 100, 2);
            $displayProgress = sprintf('%s/%s', $import->processed_rows ?? 0, $import->total_rows ?? 0);
            $progresses[] = compact('percentageProcessedRows', 'displayProgress');
        });

        return view('livewire.progress-bar', compact('progresses', 'modelClass'));
    }
}
