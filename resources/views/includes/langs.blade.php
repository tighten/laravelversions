@if (Config::has('localized-routes.supported-locales'))
    
<ul class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8 -mt-9 float-right">
    @foreach (Config::get('localized-routes.supported-locales', []) as $lang)
        <li class="inline-block">
            <a hreflang="{{ $lang }}" href="{{ \Route::localizedUrl($lang) }}" title="{{ $lang }}">
            <span class="flag-icon flag-icon-{{ $lang }}"></span>
            </a>
        </li>
    @endforeach
</ul>

@endif
