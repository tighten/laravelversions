<header class="py-8 bg-gray-700">
    <div class="flex flex-col max-w-screen-xl px-4 mx-auto sm:items-center sm:justify-between sm:flex-row-reverse sm:px-6 lg:px-8">

        @if (Config::has('localized-routes.supported_locales'))
            <x-language-selector />
        @endif

        <div class="mb-2 text-3xl">
            <a href="{{ route('versions.index') }}" class="inline-block"><img class="w-full h-full" src="/svg/logo.svg" style="width: 295px;" alt="Laravel Versions Logo"></a>
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
