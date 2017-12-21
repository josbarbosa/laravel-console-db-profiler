<?php namespace JosBarbosa\ConsoleDbProfiler;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use JosBarbosa\ConsoleDbProfiler\Classes\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
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
    public function boot(): void
    {
        $this->publishes([h::configPath() => config_path('console-db-profiler.php')], 'config');

        /** Add language files to the ConsoleDbProfiler namespace */
        Lang::addNamespace('ConsoleDbProfiler', __DIR__ . '/../lang');

        $this->publishes([
            __DIR__ . '/../lang' => resource_path('lang/vendor/ConsoleDbProfiler'),
        ]);

        if ($this->isEnvironmentEnabled() && $this->isProfilerEnabled()) {
            $this->app->make(ConsoleDbProfiler::class)->boot();
        }
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(h::configPath(), 'console-db-profiler');

        if ($this->isEnvironmentEnabled() && $this->isProfilerEnabled()) {
            $this->app->bind(ConsoleDbProfiler::class, function () {
                return new ConsoleDbProfiler($this->app);
            });

            $this->app->bind(Log::class, function () {
                $path = h::getConfig('log.options.path');
                $logger = new Logger('profiler_log');
                $lineFormatter = (new LineFormatter("%message% %context% %extra%\n", null, true, true));
                $streamHandler = (new StreamHandler($path))->setFormatter($lineFormatter);
                $logger->pushHandler($streamHandler);
                return new Log($logger, $logger::DEBUG);
            });
        }
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
