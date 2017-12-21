<?php namespace JosBarbosa\ConsoleDbProfiler\Helpers;

use Illuminate\Support\Facades\Config;

class Helper
{
    /**
     * @return string
     */
    public static function configPath(): string
    {
        return __DIR__ . '/../../config/console-db-profiler.php';
    }


    /**
     * @param string $key
     * @return mixed
     */
    public static function getConfig(string $key)
    {
        return Config::get('console-db-profiler.' . $key);
    }

    /**
     * @param string $key
     * @param $value
     */
    public static function setConfig(string $key, $value): void
    {
        Config::set('console-db-profiler.' . $key, $value);
    }

    /**
     * @param string $key
     * @param array $replace
     * @param string $locale
     * @return string
     */
    public static function trans(string $key, array $replace = [], string $locale = 'en'): string
    {
        return trans('ConsoleDbProfiler::profiler.' . $key, $replace, $locale);
    }

    /**
     * @param string $text
     * @param int $count
     * @return string
     */
    public static function strPlural(string $text, int $count): string
    {
        if (app()->getLocale() == 'en') {
            $text = str_plural($text, $count);
        }

        return $text;
    }
}
