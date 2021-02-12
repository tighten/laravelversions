@foreach (Config::get('localized-routes.supported-locales', []) as $lang)
    <link rel="alternate" hreflang="{{ $lang }}" href="{{ \Route::localizedUrl($lang) }}" />
@endforeach