<?php

namespace App\Http\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CsvImporter extends Component
{
    use WithFileUploads;
    private $maxFileSize = 1024 * 50;
    public $modelClass;
    public $model;
    public $csv;
    public $headerInput = [];
    public $headerColumns = [];

    public function updatedCsv()
    {
        $this->validateOnly('csv');

        if ($handle = fopen($this->csv->getRealPath(), "r")) {
            $this->headerColumns = fgetcsv($handle, 1000, ",");
//            $this->headerInput = $this->headerColumns;
        }
    }

    protected function rules()
    {
        $maxFileSize = $this->maxFileSize;
        $values = implode(',',$this->headerColumns);
        return [
            'csv' => ['required', 'file', 'mimes:csv', "max:$maxFileSize"],
//            'headerInput.*' => "nullable|array:$values",
            'headerInput' => "required"
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }


    public function import()
    {
        $validatedData = $this->validate();
        dd($validatedData);
    }

    public function mount($modelClass)
    {
        $fragments = explode('\\', $modelClass);
        $this->model =  $fragments[count($fragments) - 1];
    }

    public function render()
    {
        return view('livewire.csv-importer');
    }
}
