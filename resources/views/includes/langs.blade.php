@if (Config::has('localized-routes.supported-locales'))
    <select name="forma" onchange="location = this.value;" class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8 -mt-9 float-right lang">
        @foreach (Config::get('localized-routes.supported-locales', []) as $lang)
            <option value="{{ Route::localizedUrl($lang) }}" @if (App::getLocale() == $lang) selected="selected" @endif class="lang-name lang-name-{{ $lang }}">
                {{ Config::get("localized-routes.locales-name-native.$lang", Str::upper($lang)) }}
            </option>
        @endforeach
   </select>
@endif
