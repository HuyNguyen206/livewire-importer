<?php

namespace App\Helper;

use Illuminate\Support\Carbon;

class ChunkIterator
{

    public function __construct(protected \Iterator $iterator, protected int $chunk)
    {
    }

    public function get()
    {
        $chunk = [];

        while($this->iterator->valid()) {
            $record = $this->iterator->current();
            $record['created_at'] = Carbon::createFromFormat('d/m/Y h:i:s', $record['created_at'])->format('Y-m-d h:i:s');
            $record['updated_at'] = Carbon::createFromFormat('d/m/Y  h:i:s', $record['updated_at'])->format('Y-m-d h:i:s');
            $record['birthday'] = Carbon::createFromFormat('d/m/Y', $record['birthday'])->format('Y-m-d h:i:s');
            $chunk[] = $record;
            $this->iterator->next();
            if(count($chunk) === $this->chunk) {
                yield $chunk;
                $chunk = [];
            }
        }
        if (count($chunk)) {
            yield $chunk;
        }
    }
}
