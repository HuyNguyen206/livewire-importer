<?php

namespace App\Jobs;

use App\Helper\ChunkIterator;
use App\Models\Import;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;
use League\Csv\Statement;

class ChunkAndDispatchCsvImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tries = 1;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected $csvRealPath, protected Import $import, protected array $selectedColumns)
    {
        //
    }

    public function csvReader()
    {
        $csv = Reader::createFromStream(fopen($this->csvRealPath, "r"));
        $csv->setHeaderOffset(0);

        return $csv;
    }

    public function csvRecords()
    {
        return Statement::create()->process($this->csvReader());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $chunkIterator = new ChunkIterator($this->csvRecords()->getRecords(), 100);
        $selectedColumns = $this->selectedColumns;
        foreach ($chunkIterator->get() as $chunk) {
            $chunkData = collect($chunk)->map(function ($record) use($selectedColumns){
                return collect($record)->only($selectedColumns)->toArray();
            })->toArray();
            dispatch(new ImportCSVRecordJob($chunkData, $this->import));
        }
    }
}
