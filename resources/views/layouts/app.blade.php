<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? $title . ' - ' : '' }}Laravel Versions</title>

        <meta name="description" content="Security and bug fix timelines for all Laravel Versions">

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
    <body class="font-sans antialiased bg-gray-100">
        @include ('partials/modules/header')
        <div class="max-w-screen-xl px-4 pt-12 mx-auto sm:px-6 lg:px-8">
            <main>
                {{ $slot }}
            </main>
            @include ('partials/modules/footer')           
        </div>        
    </body>
</html>
