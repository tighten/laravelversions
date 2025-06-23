<?php

namespace App\View\Components;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class LanguageSelector extends Component
{
    public $current_language;

    public $formatted_languages;

    public function __construct()
    {
        $this->formatted_languages = collect(Config::get('localized-routes.supported_locales', []))->map(function ($lang) {
            return [
                'language_name' => $lang,
                'language_name_native' => Config::get("localized-routes.locales-name-native.{$lang}", Str::upper($lang)),
                'language_url' => Route::localizedUrl($lang),
            ];
        });

        $this->current_language = $this->formatted_languages->first(function ($languageObject) {
            return $languageObject['language_name'] === App::getLocale();
        })['language_name_native'];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.language-selector');
    }
}
