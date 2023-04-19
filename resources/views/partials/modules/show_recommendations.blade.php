<section>
    <h2 class="text-xl font-bold">{{ __('Recommendation') }}:</h2>
    <p class="mb-6 text-lg">
        @if ($version->status == App\Models\LaravelVersion::STATUS_ACTIVE)
            {{ __('Keep patch updated.') }}
        @elseif ($version->status == App\Models\LaravelVersion::STATUS_SECURITY)
            {{ __('Update to the latest major or LTS release.') }}
        @elseif ($version->status == App\Models\LaravelVersion::STATUS_FUTURE)
            {!! __('This version of the release is planned only and <strong>not released yet!</strong>') !!}<br>
            {{ __('The estimated release date is :date', ['date' => 'Q' . $version->released_at->quarter . ' ' . $version->released_at->year]) }}
        @else
            @php
                $recommendation = (new App\LowestSupportedVersion)($version);
            @endphp
            {!! __('Update <em>at least</em> to a security-maintained version <strong>as soon as possible!</strong>') !!}<br>
            {!! __('The lowest version still getting security fixes is: :link', [
                'link' => '<a href="' . route('versions.show', [$recommendation->major]) . '" class="border-hover">' . $recommendation->major . '</a>'])
            !!}<br>
            {!! __('To upgrade, follow the instructions in the docs or use :link-laravelshifts to upgrade automatically.', [
                'link-laravelshifts' => '<a href="https://laravelshift.com/shifts?version=' . $version->majorish . '" class="border-hover">Laravel Shift</a>'
                ])
            !!}

        @endif
    </p>
    <div class="">
        <h2 class="text-xl font-bold">{{ __('Latest Patch Release') }}:</h2>
        @php
            $latest = (count($releases)) ? $releases->first() : $version;
        @endphp
        <p class="mb-6 text-lg"><a href="#{{ $latest }}">{{ $latest }}</a></p>
    </div>
</section>
