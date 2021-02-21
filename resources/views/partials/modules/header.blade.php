<header class="py-8 bg-gray-700">
    <div class="max-w-screen-xl px-4 mx-auto sm:px-6 lg:px-8">
        @if (Config::has('localized-routes.supported-locales'))
            <select onchange="location = this.value;" class="float-right max-w-6xl px-4 mx-auto text-xs text-black bg-gray-200 border border-gray-200 rounded sm:px-6 lg:px-8 lang">
                @foreach (Config::get('localized-routes.supported-locales', []) as $lang)
                    <option value="{{ Route::localizedUrl($lang) }}" @if (App::getLocale() == $lang) selected="selected" @endif class="lang-name lang-name-{{ $lang }}">
                        {{ Config::get("localized-routes.locales-name-native.{$lang}", Str::upper($lang)) }}
                    </option>
                @endforeach
            </select>
        @endif
        <h1 class="mb-2 text-3xl">
            <a href="{{ route('versions.index') }}" class="inline-block w-64"><img class="w-full h-full" src="/svg/logo.svg" alt="Laravel Versions Logo"></a>
        </h1>
    </div>
    <div class=" {{ Route::is('*.versions.index') ? 'max-w-screen-xl px-4 py-4 mx-auto text-base text-white sm:px-6 lg:px-8' : 'hidden' }}">
        <p class="mb-4 md:mb-2">
            {{ __('Release dates and timelines for security and bug fixes for all versions of Laravel.') }}
        </p>
        <p>
            {!! __("To learn more about Laravel's versioning strategy, check out the :link.", ['link' => '<a href="https://laravel-news.com/laravel-releases" class="text-blue-800 underline hover:text-blue-600">' . __('Laravel News "Laravel Releases" page') . '</a>']) !!}
        </p>
    </div>
</header>
