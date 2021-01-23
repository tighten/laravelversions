<x-app-layout :title="$path">
    <h1 class="block text-5xl text-bold">Laravel Version: <span>{{ $path }}</span></h1>
    <a href="/" class="block mb-6 underline">(see all versions)</a>

    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            @php
                $statusText = [
                    'active' => 'Active support',
                    'security' => 'Security fixes only',
                    'inactive' => 'Not receiving bug or security fixes',
                ];

                $recommendationText = [
                    'active' => 'Keep patch updated.',
                    'security' => 'Update to the latest major or LTS release.',
                    'inactive' => 'Update <em>at least</em> to a security-maintained version <strong>as soon as possible!</strong>',
                ];
            @endphp
                <h2 class="text-xl font-bold">Status:</h2>
                <p class="mb-6 text-lg">{{ $statusText[$version->status] }}</p>

                <h2 class="text-xl font-bold">Recommendation:</h2>
                <p class="mb-6 text-lg">{{ $recommendationText[$version->status] }}</p>
{{--
                <h2 class="text-xl font-bold">Latest Patch Release:</h2>
                <p class="mb-6 text-lg">@todo</p>
--}}

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
                            'active' => 'bg-green-300',
                            'security' => 'bg-yellow-300',
                            'inactive' => 'bg-red-300',
                        ];
                        $statusTextMap = [
                            'active' => 'ALL',
                            'security' => 'SEC',
                            'inactive' => 'EOL',
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
