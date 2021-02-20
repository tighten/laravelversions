<section class="overflow-hidden border-gray-200 shadow sm:rounded-lg">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                {{ __('Major Version') }}
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                {{ __('Release date') }}
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                {{ __('Bug Fixes Until') }}
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                {{ __('Security Fixes Until') }}
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                {{ __('LTS?') }}
            </th>
            <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                {{ __('Status') }}
            </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @php
            $statusClassMap = [
                App\Models\LaravelVersion::STATUS_FUTURE => 'bg-blue-300 rounded-md inline-block px-2 py-1 inline-block ml-5 font-bold',
                App\Models\LaravelVersion::STATUS_ACTIVE => 'bg-green-300 rounded-md inline-block px-2 py-1 inline-block ml-5 font-bold',
                App\Models\LaravelVersion::STATUS_SECURITY => 'bg-yellow-300 rounded-md inline-block px-2 py-1 inline-block ml-5 font-bold',
                App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'bg-red-300 rounded-md inline-block px-3 py-2 inline-block ml-5 font-bold',
            ];
            $statusTextMap = [
                App\Models\LaravelVersion::STATUS_FUTURE => 'FUT',
                App\Models\LaravelVersion::STATUS_ACTIVE => 'ALL',
                App\Models\LaravelVersion::STATUS_SECURITY => 'SEC',
                App\Models\LaravelVersion::STATUS_ENDOFLIFE => 'EOL',
            ];
            @endphp

            @foreach ([$version] as $version)
                <tr>
                    <th class="px-6 py-4 text-sm font-medium text-left text-gray-900 whitespace-nowrap">
                        {{ $version->major < 6 ? $version->major . '.' . $version->minor : $version->major }} {{ $version->released_at->gt(now()) ? '(' . __('not released yet!') . ')' : '' }}
                    </th>
                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                        {{ $version->released_at->gt(now()) ? $version->released_at->format('F, Y') . ' (' . __('estimated') . ')' : $version->released_at->format('F j, Y') }}
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
                    <td scope="col">
                        <div class="{{ $statusClassMap[$version->status] }}">
                            <span>{{ $statusTextMap[$version->status] }}</span>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</section>
