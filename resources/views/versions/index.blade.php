<x-app-layout>
    @php
    $statusClassMap = [
    App\Models\LaravelVersion::STATUS_FUTURE => 'bg-blue-300 rounded-md inline-block px-3 py-1 inline-block ml-5 font-bold',
    App\Models\LaravelVersion::STATUS_ACTIVE => 'bg-green-300 rounded-md inline-block px-3 py-1 inline-block ml-5 font-bold',
    App\Models\LaravelVersion::STATUS_SECURITY => 'bg-yellow-300 rounded-md inline-block px-3 py-1 inline-block ml-5 font-bold',
    App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'bg-red-300 rounded-md inline-block px-3 py-2 inline-block ml-5 font-bold',
    ];
    $statusTextMap = [
    App\Models\LaravelVersion::STATUS_FUTURE => 'FUT',
    App\Models\LaravelVersion::STATUS_ACTIVE => 'ALL',
    App\Models\LaravelVersion::STATUS_SECURITY => 'SEC',
    App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'EOL',
    ];
    @endphp

    @include('partials/modules/color_key')
    @include('partials/tables/current_table')
    @include('partials/tables/eol_table')

    <div class="text-center">
        {!! __('You can also find a list of all of the security advisories for Laravel here: :link', [
        'link' => '<a class="text-blue-600 underline" href="https://github.com/FriendsOfPHP/security-advisories/tree/master/laravel/framework">https://github.com/FriendsOfPHP/security-advisories/tree/master/laravel/framework</a>'
        ]);
        !!}
    </div>

    <div class="mt-6 text-center">
        {!! __('A list of currently supported PHP versions can be found here: :link', [
        'link' => '<a class="text-blue-600 underline" href="https://phpreleases.com/">https://phpreleases.com/</a>'
        ]);
        !!}
    </div>
</x-app-layout>
