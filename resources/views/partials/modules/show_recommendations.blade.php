<section>
    <h2 class="text-xl font-bold">{{ __('Recommendation') }}:</h2>
    <p class="mb-6 text-lg">
        @if ($version->status == App\Models\LaravelVersion::STATUS_ACTIVE)
            {{ __('Keep patch updated.') }}.
        @elseif ($version->status == App\Models\LaravelVersion::STATUS_SECURITY)
            {{ __('Update to the latest major or LTS release.') }}
        @elseif ($version->status == App\Models\LaravelVersion::STATUS_FUTURE)
            {!! __('This version of the release is planned only and <strong>not released yet!</strong>') !!}<br>
            {{ __('The estimated release date is :date', ['date' => $version->released_at->format('F Y')]) }}
        @else
            @php
                $recommendation = (new App\LowestSupportedVersion)($version);
            @endphp
            {!! __('Update <em>at least</em> to a security-maintained version <strong>as soon as possible!</strong><br>
            The lowest version still getting security fixes is: :link <br>
            To upgrade, follow the instructions in the docs or use :link-laravelshifts to upgrade automatically.', [
                'link' => '<a href="' . route('versions.show', [$recommendation->major]) . '" class="text-blue-800 underline hover:text-blue-600">' . $recommendation->major . '</a>',
                'link-laravelshifts' => '<a href="https://laravelshift.com/shifts?version=' . $version->majorish . '" class="text-blue-800 underline hover:text-blue-600">Laravel Shift</a>'
                ]) !!}
        @endif
    </p>

    <div class="mb-16 lg:mb-24">
        <h2 class="text-xl font-bold">{{ __('Latest Patch Release') }}:</h2>
        <p>{{ $version }}</p>
    </div>
</section>
