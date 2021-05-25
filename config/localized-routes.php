<?php

return [

    /**
     * The locales you wish to support.
     */
    'supported-locales' => [
        'en',
        'de',
        'es',
        'fr',
        'it',
        'lt',
        'pl',
        'pt_BR',
        'uk',
        'ru',
        'vi',
        'ja',
    ],

    /**
     * Native language name
     */
    'locales-name-native' => [
        'en' => 'English',
        'de' => 'Deutsch',
        'es' => 'Español',
        'fr' => 'Français',
        'it' => 'Italiano',
        'lt' => 'Lietuvių',
        'pl' => 'Polski',
        'pt_BR' => 'Português (Brasil)',
        'uk' => 'Українська',
        'ru' => 'Русский',
        'vi' => 'Tiếng Việt',
        'ja' => '日本語',
    ],

    /**
     * If you have a main locale and don't want
     * to prefix it in the URL, specify it here.
     *
     * 'omit_url_prefix_for_locale' => 'en',
     */
    // 'omit_url_prefix_for_locale' => 'en',

    /**
     * So the home page / would be redirected to /en
     * if the active locale is en and the /en route exists.
     * And /about would redirect to /en/about.
     * 
     * 'redirect_to_localized_urls' => true,
     */
    'redirect_to_localized_urls' => true,

    /**
     * If you want to automatically set the locale
     * for localized routes set this to true.
     */
    'use_locale_middleware' => true,

    /**
     * If true, this package will use 'codezero/laravel-localizer'
     * to detect and set the preferred supported locale.
     *
     * For non-localized routes, it will look for a locale in the URL,
     * in the session, in a cookie, in the browser or in the app config.
     * This can be very useful if you have a generic home page.
     *
     * If a locale is detected, it will be stored in the session,
     * in a cookie and as the app locale.
     *
     * If you disable this option, only localized routes will have a locale
     * and only the app locale will be set (so not in the session or cookie).
     *
     * You can publish its config file and tweak it for your needs.
     * This package will only override its 'supported-locales' option
     * with the 'supported-locales' option in this file.
     *
     * For more info, visit:
     * https://github.com/codezero-be/laravel-localizer
     *
     * This option only has effect if you use the SetLocale middleware.
     */
    'use_localizer' => true,

];
