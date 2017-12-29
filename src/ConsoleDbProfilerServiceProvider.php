<?php namespace JosBarbosa\ConsoleDbProfiler;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class ConsoleDbProfilerServiceProvider
 * @package JosBarbosa\ConsoleDbProfiler
 */
class ConsoleDbProfilerServiceProvider extends ServiceProvider
{
    /**
     * Service provider boot method
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([h::configPath() => config_path('console-db-profiler.php')], 'config');

        /** Add language files to the ConsoleDbProfiler namespace */
        Lang::addNamespace('ConsoleDbProfiler', __DIR__ . '/../lang');

        $this->publishes([
            __DIR__ . '/../lang' => resource_path('lang/vendor/ConsoleDbProfiler'),
        ]);

        if ($this->isEnvironmentEnabled() && $this->isProfilerEnabled()) {
            (new ConsoleDbProfiler($this->app))->boot();
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(h::configPath(), 'console-db-profiler');
    }

    /**
     * @return bool
     */
    protected function isEnvironmentEnabled(): bool
    {
        if (h::getConfig('environment.' . $this->app->environment()) === true || $this->app->isLocal()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function isProfilerEnabled(): bool
    {
        if (!$this->app->runningUnitTests() &&
            $this->app->runningInConsole() &&
            (h::getConfig('enabled') || $this->hasParameterOption('-vvv'))) {
            return true;
        }
        return false;
    }

    /**
     * @param string $option
     * @return bool
     */
    protected function hasParameterOption(string $option): bool
    {
        return ((new ArgvInput())->hasParameterOption($option));
    }
}
