<div class="mx-5">
        <div wire:poll.visible.1000ms>
            @foreach($progresses as $model => $progress)
                <span class="font-medium">Importing {{$modelClass}}</span>
                <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{$progress['percentageProcessedRows']}}%"> {{$progress['displayProgress']}}</div>
                </div>
            @endforeach
        </div>
</div>
