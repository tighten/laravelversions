<x-app-layout>
    <h1 class="block mb-4 text-5xl text-bold">Laravel Version: <span>{{ $path }}</span></h1>

    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <h2 class="text-xl font-bold">Status:</h2>
                <p class="mb-12 text-lg">
                @switch($version->status)
                    @case('active')
                        Active Support
                        @break
                    @case('security')
                        Security Fixes Only
                        @break
                    @case('inactive')
                        No bug or security fixes! <strong>Upgrade as soon as possible.</strong>
                        @break
                @endswitch
                </p>
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

                <br>
            <!--

                // todo:
                - latest patch
                - status
                - dangerous?
                - recommendation
            -->

            <p class="max-w-3xl mb-6">To learn more about Laravel's versioning strategy, check out the <a href="https://laravel-news.com/laravel-releases" class="text-blue-800 underline hover:text-blue-600">Laravel news "Laravel Releases" page</a>.</p>
            </div>
        </div>
    </div>
</x-app-layout>
