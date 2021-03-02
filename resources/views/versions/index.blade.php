<x-app-layout>
    @php
        $statusClassMap = [
            App\Models\LaravelVersion::STATUS_FUTURE => 'bg-blue-300 rounded-md inline-block px-2 py-1 inline-block ml-5 font-bold',
            App\Models\LaravelVersion::STATUS_ACTIVE => 'bg-green-300 rounded-md inline-block px-2 py-1 inline-block ml-5 font-bold',
            App\Models\LaravelVersion::STATUS_SECURITY => 'bg-yellow-300 rounded-md inline-block px-2 py-1 inline-block ml-5 font-bold',
            App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'bg-red-300 rounded-md inline-block px-3 py-2 inline-block ml-5 font-bold',
        ];
        $statusTextMap = [
            App\Models\LaravelVersion::STATUS_FUTURE => __('FUT'),
            App\Models\LaravelVersion::STATUS_ACTIVE => __('ALL'),
            App\Models\LaravelVersion::STATUS_SECURITY => __('SEC'),
            App\Models\LaravelVersion::STATUS_ENDOFLIFE => __('EOL'),
        ];
    @endphp

    @include('partials/modules/color_key')
    @include('partials/tables/current_table')
    @include('partials/tables/eol_table')
</x-app-layout>
