<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Import;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class ImportCSVRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected array $chunkData, protected Import $import)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Customer::upsert($this->chunkData, ['email'], ['first_name', 'last_name', 'company', 'vip', 'birthday']);
        $import = $this->import->refresh();
        $updatedData = [
            'processed_rows' => $currentProcessRows = $import->processed_rows + count($this->chunkData)
        ];
        if ($currentProcessRows === $import->total_rows) {
            $updatedData += ['completed_at' => now()];
        }
        $import->update($updatedData);
    }

    public function middleware()
    {
        return [(new WithoutOverlapping($this->import->id))->expireAfter(2)];
    }
}
