<x-app-layout :title="$path">
    <h1 class="block mb-4 font-bold sm:test-5xl">
        {{ __('Laravel Version') }}:
        <span>
            {{ $path }}
        </span>
    </h1>
    <div class="flex flex-col h-screen">
        @include('partials/modules/show_status')
        @include('partials/modules/show_recommendations')
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                @include('partials/tables/show_table')
            </div>
        </div>
    </div>
</x-app-layout>
