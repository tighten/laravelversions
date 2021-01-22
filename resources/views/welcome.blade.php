<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Versions</title>

        <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="antialiased font-sans bg-gray-100 pt-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <h1 class="block text-4xl text-bold mb-4">Laravel Versions</h1>
            <p class="max-w-2xl mb-4">For LTS releases, such as Laravel 6, bug fixes are provided for 2 years and security fixes are provided for 3 years. These releases provide the longest window of support and maintenance. For general releases, bug fixes are provided for 7 months and security fixes are provided for 1 year. For all additional libraries, including Lumen, only the latest release receives bug fixes.</p>
            <p class="max-w-2xl mb-8">To learn more, check out the <a href="https://laravel-news.com/laravel-releases" class="text-blue-800 underline hover:text-blue-600">Laravel news "Laravel Releases" page</a>.</p>
            <br>

            <div class="mb-8">
                <div class="font-bold">Colors:</div>

                <div class="max-w-xs">
                    <div class="p-2 bg-red-300">End of Life</div>
                    <div class="p-2 bg-yellow-300">Security fixes only</div>
                    <div class="p-2 bg-green-300">Bug and security fixes</div>
                </div>
            </div>

            <h2 class="block text-xl font-bold mb-2">Currently supported versions</h2>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg mb-8">
                            <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Version
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Release date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bug Fixes Until
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Security Fixes Until
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                ]
                            @endphp
                            @foreach ($activeVersions as $version)
                                <tr>
                                    <th scope="col" class="{{ $statusClassMap[$version->status] }}">&nbsp;</th>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $version->major }} {{ $version->released_at->gt(now()) ? '(not released yet!)' : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $version->released_at->format('F j, Y') }} {{ $version->released_at->gt(now()) ? '(estimated)' : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $version->ends_bugfixes_at ? $version->ends_bugfixes_at->format('F j, Y'): '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $version->ends_securityfixes_at ? $version->ends_securityfixes_at->format('F j, Y') : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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

            <h2 class="block text-xl font-bold mb-2">No longer receiving security updates!</h2>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg mb-8">
                            <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Version
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Release date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Bug Fixes Until
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Security Fixes Until
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        LTS?
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($inactiveVersions as $version)
                                <tr>
                                    <th scope="col" class="{{ $statusClassMap[$version->status] }}">&nbsp;</th>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $version->major }}{{ $version->major < 6 ? '.' . $version->minor : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $version->released_at->format('F j, Y') }} {{ $version->released_at->gt(now()) ? '(estimated)' : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $version->ends_bugfixes_at ? $version->ends_bugfixes_at->format('F j, Y'): '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $version->ends_securityfixes_at ? $version->ends_securityfixes_at->format('F j, Y') : '' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
            <div class="text-center my-8">
                Brought to you by the lovely folks at <a href="https://tighten.co/" class="text-blue-800 hover:text-blue-600 underline">Tighten</a>.
            </div>
        </div>
    </body>
</html>
