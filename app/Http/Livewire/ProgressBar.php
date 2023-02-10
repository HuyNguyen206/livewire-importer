<?php

namespace App\Http\Livewire;

use App\Models\Import;
use Livewire\Component;

class ProgressBar extends Component
{
    public $modelClass;
    public $isShow = false;

    protected $listeners = ['showProgressBar'];

    public function showProgressBar()
    {
        $this->isShow = true;
        $this->render();
    }

    public function render()
    {
        $info = Import::query()->where('model', $this->modelClass)->latest()->first();
        $percentageProcessedRows = $info ? round(($info->processed_rows / $info->total_rows) * 100, 2).'%': 0 .'%';
        $displayProgress = sprintf('%s/%s', $info->processed_rows ?? 0, $info->total_rows ?? 0);
        return view('livewire.progress-bar', compact('info', 'percentageProcessedRows', 'displayProgress'));
    }
}
