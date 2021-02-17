<section>
    <h2 class="text-xl font-bold">Recommendation:</h2>
    <p class="mb-6 text-lg">
        @if ($version->status == App\Models\LaravelVersion::STATUS_ACTIVE)
            Keep patch updated.
        @elseif ($version->status == App\Models\LaravelVersion::STATUS_SECURITY)
            Update to the latest major or LTS release.
        @elseif ($version->status == App\Models\LaravelVersion::STATUS_FUTURE)
            This version of the release is planned only and <strong>not released yet!</strong><br>
            The estimated release date is {{ $version->released_at->format('F Y') }}
        @else
            @php
                $recommendation = (new App\LowestSupportedVersion)($version);
            @endphp
            Update <em>at least</em> to a security-maintained version <strong>as soon as possible!</strong><br>
            The lowest version still getting security fixes is: <a href="{{ route('versions.show', [$recommendation->major]) }}" class="font-bold text-blue-800 border-hover">{{ $recommendation->major  }}</a>
        @endif
    </p>

    <div class="mb-16 lg:mb-24">
        <h2 class="text-xl font-bold">Latest Patch Release:</h2>
        <p>{{ $version }}</p>
    </div>
</section>
