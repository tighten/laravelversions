<section class="flex flex-col py-6">
    <h2 class="block mb-1 text-xl font-bold">{{ __('No longer receiving security updates!') }}</h2>
    <p class="mb-6">
        {!! __('Need help upgrading your app? Try :link-laravelshift for automated upgrades or :link-tighten if you need more than just upgrades.', [
            'link-laravelshift' => '<a href="https://laravelshift.com/" class="text-blue-600 hover:text-blue-500 border-hover">Laravel Shift</a>',
            'link-tighten' => '<a href="https://tighten.co/" class="text-blue-600 hover:text-blue-500 border-hover">' . __('contact Tighten') . '</a>',
        ]) !!}
    </p>
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
            <div class="mb-8 overflow-hidden border-gray-200 shadow sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="py-3 pl-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Version') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Release date') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Bug Fixes Until') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase lg:pl-8">
                                {{ __('Security Fixes Until') }}
                            </th>
                            <th scope="col" class="py-3 pl-6 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                {{ __('Status') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                        @foreach ($inactiveVersions as $version)
                            <tr>
                                <th class="px-6 py-4 text-sm font-medium text-left text-gray-900 whitespace-nowrap">
                                    <a href="{{ $version->url }}" class="border-hover">{{ $version->major }}{{
                                        $version->major < 6
                                            ? '.' . $version->minor
                                            : ''
                                    }}</a>
                                </th>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{
                                        $version->released_at->translatedFormat(__('DateLongFormat')) }} {{ $version->released_at->gt(now())
                                            ? '(' . __('estimated') . ')'
                                            : ''
                                    }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{
                                        $version->ends_bugfixes_at
                                            ? $version->ends_bugfixes_at->translatedFormat(__('DateLongFormat'))
                                            : ''
                                    }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{
                                        $version->ends_securityfixes_at
                                            ? $version->ends_securityfixes_at->translatedFormat(__('DateLongFormat'))
                                            : ''
                                    }}
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
