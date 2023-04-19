@if (count($releases) > 1)
<div class="mt-6">
    <h2 class="mb-3 text-xl font-bold">Releases</h2>

    <div class="flex flex-wrap items-stretch mb-6 space-x-2 space-y-2">
        <span></span><!-- This is a hack to fix issue with first release height -->
        @foreach ($releases as $release)
            <a href="#{{ $release }}" class="rounded bg-gray-200 px-2 py-0.5 text-xs text-gray-700">{{ $release }}</a>
        @endforeach
    </div>

    <div class="mb-6">
        @foreach ($releases as $release)
            <a name="{{ $release }}"></a>
            <div class="mb-6 overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                <div class="min-w-full">
                    <div class="px-6 py-4 text-sm font-medium text-gray-500 bg-gray-50">
                        {{ $release }}
                    </div>
                    <div class="px-6 py-4 bg-white">
                        <div class="prose-sm prose">
                            {!! $release->changelog !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
