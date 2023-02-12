<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customer
        </h2>
    </x-slot>

    <div class="py-12" x-data="{open:false}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" >
            <button @click.prevent="open = true" class="mb-4 bg-blue-500 px-4 py-2 text-white rounded-xl">Open Importer</button>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @foreach($customers as $customer)
                        <div>
                            {{$customer->id}}. {{$customer->email}}
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
        <div x-show="open" x-cloak>
            <livewire:csv-importer :modelClass="$modelClass"/>
        </div>
    </div>
</x-app-layout>
