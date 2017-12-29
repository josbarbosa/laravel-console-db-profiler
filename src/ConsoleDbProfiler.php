<?php namespace JosBarbosa\ConsoleDbProfiler;

use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Event;
use JosBarbosa\ConsoleDbProfiler\Classes\Log;
use JosBarbosa\ConsoleDbProfiler\Collectors\Hint;
use JosBarbosa\ConsoleDbProfiler\Collectors\Profile;
use JosBarbosa\ConsoleDbProfiler\Classes\Query;
use JosBarbosa\ConsoleDbProfiler\Collectors\Typology;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputHintsTable;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputProfileTable;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputTotalsTable;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputTypologiesTable;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class ConsoleDbProfiler
 * @package JosBarbosa\ConsoleDbProfiler
 */
class ConsoleDbProfiler
{
    /**
     * The Laravel application instance.
     *
     * @var Application $app
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function boot()
    {
        /** @var Profile $profiles */
        $profiles = $this->app->make(Profile::class);
        /** @var Hint $hints */
        $hints = $this->app->make(Hint::class);
        /** @var Typology $typologies */
        $typologies = $this->app->make(Typology::class);

        Event::listen(
            QueryExecuted::class,
            function (QueryExecuted $queryExecuted) use ($profiles, $hints, $typologies) {
                $sql = $this->applyBindings($queryExecuted->sql, $queryExecuted->bindings);
                $query = new Query($sql, $queryExecuted->time, $queryExecuted->connectionName);

                if (h::getConfig('log.enabled')) {
                    $this->log($query, $profiles);
                }

                $profiles->collect($query);

                if (h::getConfig('hints')) {
                    $hints->collect($sql);
                }

                if (h::getConfig('typologies')) {
                    $typologies->collect($sql);
                }
            }
        );

        $this->app->terminating(function () use ($profiles, $hints, $typologies) {
            if ($profiles->collection()->isNotEmpty()) {
                (new OutputProfileTable($profiles))->handle();
                (new OutputTotalsTable($profiles))->handle();
                (new OutputTypologiesTable($typologies))->handle();
                (new OutputHintsTable($hints))->handle();
            }
        });
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return string
     */
    public static function applyBindings(string $query, array $bindings): string
    {
        foreach ($bindings as $binding) {
            if ($binding instanceof Carbon) {
                $binding = (string)$binding;
            }
            $query = substr_replace(
                $query,
                (gettype($binding) == 'string' ? '\'' . $binding . '\'' : $binding),
                strpos($query, '?'),
                1
            );
        }

        return $query;
    }

    /**
     * @param Query $query
     * @param Profile $profiles
     */
    protected function log(Query $query, Profile $profiles)
    {
        $log = new Log(h::getConfig('log.options.path'));
        if ($profiles->collection()->isEmpty()) {
            if (!h::getConfig('log.options.append')) {
                $log->delete();
            }

            $date = Carbon::now()->toDateTimeString();
            $commandName = $this->getArtisanCommandName();
            $log->header("[{$date}] Console DB Profiler {$commandName}");
        }

        $log->save($query->getSql());
    }

    /**
     * @return string
     */
    protected function getArtisanCommandName(): string
    {
        return array_get(request()->server(), 'argv.1') ?? '';
    }
}
