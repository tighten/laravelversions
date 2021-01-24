<x-app-layout :title="$path">
    <h1 class="block text-5xl text-bold">Laravel Version: <span>{{ $path }}</span></h1>
    <a href="/" class="block mb-6 underline">(see all versions)</a>

    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            @php
                $statusText = [
                    App\Models\LaravelVersion::STATUS_ACTIVE => 'Active support',
                    App\Models\LaravelVersion::STATUS_SECURITY => 'Security fixes only',
                    App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'Not receiving bug or security fixes',
                ];

                $recommendationText = [
                    App\Models\LaravelVersion::STATUS_ACTIVE => 'Keep patch updated.',
                    App\Models\LaravelVersion::STATUS_SECURITY => 'Update to the latest major or LTS release.',
                    App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'Update <em>at least</em> to a security-maintained version <strong>as soon as possible!</strong>',
                ];
            @endphp
                <h2 class="text-xl font-bold">Status:</h2>
                <p class="mb-6 text-lg">{{ $statusText[$version->status] }}</p>

                <h2 class="text-xl font-bold">Recommendation:</h2>
                <p class="mb-6 text-lg">
                    @if ($version->status == App\Models\LaravelVersion::STATUS_ACTIVE)
                        Keep patch updated.
                    @elseif ($version->status == App\Models\LaravelVersion::STATUS_SECURITY)
                        Update to the latest major or LTS release.
                    @else
                        @php
                            $recommendation = (new App\LowestSupportedVersion)($version);
                        @endphp
                        Update <em>at least</em> to a security-maintained version <strong>as soon as possible!</strong><br>
                        The lowest version still getting security fixes is: <a href="{{ route('versions.show', [$recommendation->major]) }}" class="text-blue-800 underline hover:text-blue-600">{{ $recommendation->major  }}</a>
                    @endif
                </p>

                <h2 class="text-xl font-bold">Latest Patch Release:</h2>
                <p class="mb-6 text-lg">{{ $version }}</p>

                <div class="mb-8 overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Major Version
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Release date
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Bug Fixes Until
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Security Fixes Until
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                LTS?
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $statusClassMap = [
                            App\Models\LaravelVersion::STATUS_ACTIVE => 'bg-green-300',
                            App\Models\LaravelVersion::STATUS_SECURITY => 'bg-yellow-300',
                            App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'bg-red-300',
                        ];
                        $statusTextMap = [
                            App\Models\LaravelVersion::STATUS_ACTIVE => 'ALL',
                            App\Models\LaravelVersion::STATUS_SECURITY => 'SEC',
                            App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'EOL',
                        ];
                    @endphp
                    @foreach ([$version] as $version)
                        <tr>
                            <th scope="col" class="{{ $statusClassMap[$version->status] }}">
                                <span class="hidden js-colorblind">{{ $statusTextMap[$version->status] }}</span>
                                &nbsp;
                            </th>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                {{ $version->major < 6 ? $version->major . '.' . $version->minor : $version->major }} {{ $version->released_at->gt(now()) ? '(not released yet!)' : '' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $version->released_at->gt(now()) ? $version->released_at->format('F, Y') . ' (estimated)' : $version->released_at->format('F j, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $version->ends_bugfixes_at ? $version->ends_bugfixes_at->format('F j, Y'): '' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $version->ends_securityfixes_at ? $version->ends_securityfixes_at->format('F j, Y') : '' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $version->is_lts ? '✓' : '' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
