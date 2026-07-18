<?php


namespace Agmedia\Helpers;


use Agmedia\Helpers\Language;
use Agmedia\Helpers\Setting;
use Illuminate\Database\DatabaseManager;

class Config
{
    
    /**
     * @return mixed
     */
    public static function getLanguage()
    {
        return Language::where('code', Setting::where('key', 'config_language')->pluck('value'))
            ->pluck('language_id');
    }
}