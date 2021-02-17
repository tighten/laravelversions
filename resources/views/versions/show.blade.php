<x-app-layout :title="$path">
    <h1 class="block mb-4 text-5xl font-bold">Laravel Version: <span>{{ $path }}</span></h1>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">

            @php
                $statusText = [
                    App\Models\LaravelVersion::STATUS_FUTURE => 'Future release',
                    App\Models\LaravelVersion::STATUS_ACTIVE => 'Active support',
                    App\Models\LaravelVersion::STATUS_SECURITY => 'Security fixes only',
                    App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'Not receiving bug or security fixes',
                ];

                $recommendationText = [
                    App\Models\LaravelVersion::STATUS_FUTURE => 'Planned release.',
                    App\Models\LaravelVersion::STATUS_ACTIVE => 'Keep patch updated.',
                    App\Models\LaravelVersion::STATUS_SECURITY => 'Update to the latest major or LTS release.',
                    App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'Update <em>at least</em> to a security-maintained version <strong>as soon as possible!</strong>',
                ];
            @endphp

            @include ('partials/modules/show_status')
            @include ('partials/modules/show_recommendations')
            @include ('partials/tables/show_table')
                           
            </div>
        </div>
    </div>
</x-app-layout>
