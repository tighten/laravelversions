<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel Versions</title>

        <meta name="description" content="Security and bug fix timelines for all Laravel Versions">

        <meta property="og:site_name" content="Laravel Versions">
        <meta property="og:locale" content="en_US">
        <meta property="og:title" content="Laravel Versions">
        <meta property="og:url" content="https://laravelversions.com/">
        <meta property="og:type" content="website">
        <meta property="og:description" content="Security and bug fix timelines for all Laravel Versions">

        <meta property="og:image" content="https://laravelversions.com/og.png">
        <meta property="og:image:height" content="630">
        <meta property="og:image:width" content="1200">

        <meta name="twitter:site" content="@laravelversions">
        <meta name="twitter:creator" content="@stauffermatt">
        <meta name="twitter:title" content="Laravel Versions">
        <meta name="twitter:description" content="Security and bug fix timelines for all Laravel Versions">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:image" content="https://laravelversions.com/og.png">

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body class="antialiased font-sans bg-gray-100 pt-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="block text-5xl text-bold mb-4">Laravel Versions</h1>
            <p class="max-w-2xl mb-8 text-lg">Release dates and security/bugfix timelines for all versions of Laravel.</p>

            <div class="mb-8 max-w-xs">
                <div class="float-right cursor-pointer hover:text-blue-800" id="colorblind-mode-toggle">Colorblind mode</div>
                <div class="font-bold">Colors:</div>

                <div class="p-2 bg-red-300">
                    <div class="float-right font-bold js-colorblind font-bold hidden">EOL</div>
                    End of Life
                </div>
                <div class="p-2 bg-yellow-300">
                    <div class="float-right font-bold js-colorblind font-bold hidden">SEC</div>
                    Security fixes only
                </div>
                <div class="p-2 bg-green-300">
                    <div class="float-right font-bold js-colorblind font-bold hidden">ALL</div>
                    Bug and security fixes
                </div>
            </div>

            <p class="max-w-3xl mb-8">To learn more about Laravel's versioning strategy, check out the <a href="https://laravel-news.com/laravel-releases" class="text-blue-800 underline hover:text-blue-600">Laravel news "Laravel Releases" page</a>.</p>

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
                                ];
                                $statusTextMap = [
                                    'active' => 'ALL',
                                    'security' => 'SEC',
                                    'inactive' => 'EOL',
                                ];
                            @endphp
                            @foreach ($activeVersions as $version)
                                <tr>
                                    <th scope="col" class="{{ $statusClassMap[$version->status] }}">
                                        <span class="js-colorblind hidden">{{ $statusTextMap[$version->status] }}</span>
                                        &nbsp;
                                    </th>
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
                                    <th scope="col" class="{{ $statusClassMap[$version->status] }}">
                                        <span class="js-colorblind hidden">{{ $statusTextMap[$version->status] }}</span>
                                        &nbsp;
                                    </th>
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
                <br>
                <a href="https://github.com/tighten/laravelversions" class="text-blue-800 hover:text-blue-600 underline">Source on GitHub</a> | <a href="/api/versions/laravel" class="text-blue-800 hover:text-blue-600 underline">Data available in JSON format</a>
            </div>
        </div>

        <script>
                if (localStorage.getItem('colorblind_mode') != 'true') {
                    localStorage.setItem('colorblind_mode', 'false');
                }

                let $button = document.getElementById('colorblind-mode-toggle');
                let $labels = document.getElementsByClassName('js-colorblind');
                let colorblind_mode = false;

                function colorBlindModeOn() {
                    $button.classList.add('font-bold');
                    for (let $label of $labels) {
                        $label.classList.remove('hidden');
                    }
                }
                function colorBlindModeOff() {
                    $button.classList.remove('font-bold');
                    for (let $label of $labels) {
                        $label.classList.add('hidden');
                    }
                }

                $button.addEventListener('click', () => {
                    if (colorblind_mode) { colorBlindModeOff() } else { colorBlindModeOn() }
                    colorblind_mode = ! colorblind_mode;
                    localStorage.setItem('colorblind_mode', colorblind_mode ? 'true' : 'false');
                });

                if (localStorage.getItem('colorblind_mode') === 'true') {
                    colorBlindModeOn();
                    colorblind_mode = true;
                }
            </script>
    </body>
</html>
