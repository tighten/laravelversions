<header class="py-8 bg-gray-700">
    <div class="flex flex-col max-w-screen-xl px-4 mx-auto sm:items-center sm:justify-between sm:flex-row-reverse sm:px-6 lg:px-8">

        @if (Config::has('localized-routes.supported-locales'))
            @php
                $formatted_languages = [];
                $current_language = null;

                foreach (Config::get('localized-routes.supported-locales', []) as $lang) {
                    $language_name_native = Config::get("localized-routes.locales-name-native.{$lang}", Str::upper($lang));
                    $formatted_languages[] = [
                        'language_code' => $lang,
                        'language_name_native' => $language_name_native,
                        'language_url' => Route::localizedUrl($lang),
                    ];
                    if (App::getLocale() == $lang) {
                        $current_language = $language_name_native;
                    }
                }            
            @endphp
            <language-select 
                :languages="{{ json_encode($formatted_languages) }}" 
                :current-language="{{ json_encode($current_language) }}">
            </language-select>
        @endif

        <div class="mb-2 text-3xl">
            <a href="{{ route('versions.index') }}" class="inline-block"><img class="w-full h-full" src="/svg/logo.svg" alt="Laravel Versions Logo"></a>
        </div>
    </div>
    <div class="{{ Route::is('*.versions.index') ? 'max-w-screen-xl px-4 py-4 mx-auto text-base text-white sm:px-6 lg:px-8' : 'hidden' }}">
        <p class="mb-4 md:mb-2">
            {{ __('Release dates and timelines for security and bug fixes for all versions of Laravel.') }}
        </p>
        <p>
            {!! __("To learn more about Laravel's versioning strategy, check out the :link.", ['link' => '<a href="https://laravel-news.com/laravel-releases" class="text-blue-300 border-hover hover:text-blue-500">' . __('Laravel News "Laravel Releases" page') . '</a>']) !!}
        </p>
    </div>
</header>
