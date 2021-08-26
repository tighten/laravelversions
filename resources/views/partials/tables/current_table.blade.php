<section class="flex flex-col py-6">
    <h2 class="block mb-6 text-xl font-bold">{{ __('Currently supported versions') }}</h2>
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="mb-8 overflow-hidden border-gray-200 shadow sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="py-3 pl-6 font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Version') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Release date') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Bug Fixes Until') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Security Fixes Until') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('LTS?') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('Status') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($activeVersions as $version)
                        <tr>
                            <th class="px-6 py-4 text-sm font-medium text-left text-gray-900 whitespace-nowrap">
                                <a href="{{ $version->url }}" class="border-hover">{{ $version->major }} {{
                                    $version->released_at->gt(now())
                                        ? '(' . __('not released yet!') . ')'
                                        : ''
                                }}</a>
                            </th>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $version->released_at->translatedFormat(__('DateLongFormat')) }}
                                {{--
                                    Retaining this in case we go back to non-specific future release dates.
                                    $version->released_at->gt(now())
                                        ? $version->released_at->translatedFormat(__('DateShortFormat')) . ' (' . __('estimated') . ')'
                                        : $version->released_at->translatedFormat(__('DateLongFormat'))
                                --}}
                            </td>
                            <td class="py-4 pl-6 text-sm text-gray-500 lg:pl-8 whitespace-nowrap">
                                @if ($version->ends_bugfixes_at)
                                {{ $version->ends_bugfixes_at->translatedFormat(__('DateLongFormat')) }}
                                {{--
                                        $version->released_at->gt(now())
                                            ? $version->ends_bugfixes_at->translatedFormat(__('DateShortFormat')) . ' (' . __('estimated') . ')'
                                            : $version->ends_bugfixes_at->translatedFormat(__('DateLongFormat'))
                                    --}}
                                @endif
                            </td>
                            <td class="py-4 pl-6 text-sm text-gray-500 lg:pl-8 whitespace-nowrap">
                                @if ($version->ends_securityfixes_at)
                                {{ $version->ends_securityfixes_at->translatedFormat(__('DateLongFormat')) }}
                                {{--
                                        $version->released_at->gt(now())
                                            ? $version->ends_securityfixes_at->translatedFormat(__('DateShortFormat')) . ' (' . __('estimated') . ')'
                                            : $version->ends_securityfixes_at->translatedFormat(__('DateLongFormat'))
                                    --}}
                                @endif
                            </td>
                            <td class="py-4 pl-6 text-sm text-gray-500 lg:pl-8 whitespace-nowrap">
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
            </div>
        </div>
    </div>
</section>
