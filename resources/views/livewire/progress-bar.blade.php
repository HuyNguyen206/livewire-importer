<div class="mx-5">
    @if($isShow)
        <div wire:poll.1000ms class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
            <h2 class="font-medium">Importing [filename]</h2>
            <div class="bg-blue-600 text-xs font-medium text-blue-100 text-center p-0.5 leading-none rounded-full" style="width: {{$percentageProcessedRows}}%"> {{$displayProgress}}</div>
        </div>
    @endif
</div>
