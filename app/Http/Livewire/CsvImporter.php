<?php

namespace App\Http\Livewire;

use App\Helper\ChunkIterator;
use App\Jobs\ImportCSVRecordJob;
use App\Models\Customer;
use App\Models\Import;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Statement;
use Livewire\Component;
use Livewire\WithFileUploads;

class CsvImporter extends Component
{
    use WithFileUploads;
    private $maxFileSize = 1024 * 170;
    public $modelClass;
    public $model;
    public $csv;
    public $headersfile= [];
    public $columns = [];
    public $hasFilledData = false;

    const REQUIRED_COLUMNS = ['id', 'email'];

    public function render()
    {
        $this->hasFilledData = $this->hasFilledData();

        return view('livewire.csv-importer');
    }

    protected function getValidationAttributes()
    {
        return collect($this->buildRuleRequiredForRequiredColumn())->mapWithKeys(function ($value, $column){
            $correctName = Str::of($column)->after('.')->toString();
            return [$column => $correctName];
        })->toArray();
    }

    public function updatedCsv()
    {
        $this->validateOnly('csv');
        $csv = $this->csvReader;
        $this->headersfile = $csv->getHeader();

        $this->columns = collect(Schema::getColumnListing('customers'))
            ->mapWithKeys(fn($column) => [$column => ''])
            ->only(['id', 'first_name', 'last_name', 'email'])
            ->toArray();
    }

    public function getCsvReaderProperty()
    {
        $csv = Reader::createFromStream(fopen($this->csv->getRealPath(), "r"));
        $csv->setHeaderOffset(0);

        return $csv;
    }

    public function getCsvRecordsProperty()
    {
        return Statement::create()->process($this->csvReader);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    protected function rules()
    {
        $maxFileSize = $this->maxFileSize;
        $values = implode(',', $this->headersfile);

        return [
            'csv' => ['required', 'file', 'mimes:csv', "max:$maxFileSize"],
//            'headerInput.*' => "nullable|array:$values",
            'columns.*' => ["in:$values"]
        ] + $this->buildRuleRequiredForRequiredColumn();
    }

    private function buildRuleRequiredForRequiredColumn()
    {
        $result = [];
        foreach (self::REQUIRED_COLUMNS as $column){
            $result["columns.$column"] = 'required';
        }

        return $result;
    }

    private function hasFilledData()
    {
        foreach ($this->columns as $column => $value) {
            if ($value) {
                return true;
            }
        }

        return false;
    }

    public function import()
    {
        $this->validate();
        auth()->user()->imports()->create([
            'model' => $this->modelClass,
            'file_path' => $this->csv->getRealPath(),
            'file_name' => $this->csv->getClientOriginalName(),
            'total_rows' => count($this->csvRecords),
            'processed_rows' => 0
        ]);
        $this->emitTo(ProgressBar::class, 'showProgressBar');
        $chunkIterator = new ChunkIterator($this->csvRecords->getRecords(), 100);
        $import = Import::query()->where('model', $this->modelClass)->latest()->first();
        foreach ($chunkIterator->get() as $chunk) {
            $chunkData = collect($chunk)->map(function ($record){
                unset($record['id']);
                return $record;
            })->toArray();
            dispatch(new ImportCSVRecordJob($chunkData, $import));
//            $batches[] = new ImportCSVRecordJob($chunkData, $import);
        }
        $this->reset(['hasFilledData', 'columns']);
//        Bus::batch($batches)
//            ->then(function () use ($import){
//                $import->touch('completed_at');
//            })
//           ->dispatch();

    }

    public function mount($modelClass)
    {
        $fragments = explode('\\', $modelClass);
        $this->model =  $fragments[count($fragments) - 1];
    }
}
