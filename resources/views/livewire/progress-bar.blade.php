<div class="mx-5">
        <div wire:poll.visible.1000ms>
            @foreach($progresses as $model => $progress)
                <span class="font-medium">Importing {{$modelClass}}</span>
                <div class="w-full bg-gray-200 rounded-full dark:bg-gray-900 relative">
                    <span class="font-semibold absolute">{{$progress['displayProgress']}}</span>
                    <div class="bg-blue-300 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full h-7" style="width: {{$progress['percentageProcessedRows']}}%"></div>
                </div>
            @endforeach
        </div>
</div>
