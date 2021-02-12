<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title . ' - ' : '' }}Laravel Versions</title>

        <meta name="description" content="{{ __('Security and bug fix timelines for all Laravel Versions') }}">

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
    <body class="pt-12 font-sans antialiased bg-gray-100">
        <div class="max-w-6xl px-4 mx-auto sm:px-6 lg:px-8">

            <main>
                {{ $slot }}
            </main>

            <div class="my-8 text-center">
                {{ __('Brought to you by the lovely folks at') }} <a href="https://tighten.co/" class="text-blue-800 underline hover:text-blue-600">Tighten</a>.
                <br>
                <a href="https://github.com/tighten/laravelversions" class="text-blue-800 underline hover:text-blue-600">{{ __('Source on GitHub') }}</a> | <a href="/api/versions" class="text-blue-800 underline hover:text-blue-600">{{ __('Data available in JSON format') }}</a>
                | <a href="https://twitter.com/laravelversions" class="text-blue-800 underline hover:text-blue-600">{{ __('Follow on Twitter for important dates') }}</a>
            </div>
        </div>

        @stack('footer-scripts')
    </body>
</html>
