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
    public $lang;

    public function __construct()
    {
        $supported_languages = Config::get('localized-routes.supported-locales', []);

        $this->formatted_languages = [];

        foreach ($supported_languages as $lang) {
            $language_name_native = Config::get("localized-routes.locales-name-native.{$lang}", Str::upper($lang));

            $this->formatted_languages[] = [
                'language_name_native' => $language_name_native,
                'language_url' => Route::localizedUrl($lang),
            ];

            if (App::getLocale() == $lang) {
                $this->current_language = $language_name_native;
            }
        }
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
