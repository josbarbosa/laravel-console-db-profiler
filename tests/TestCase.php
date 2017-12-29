<?php namespace JosBarbosa\ConsoleDbProfiler\Tests;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Support\Facades\Event;
use JosBarbosa\ConsoleDbProfiler\Classes\Query;
use JosBarbosa\ConsoleDbProfiler\ConsoleDbProfilerServiceProvider;
use Orchestra\Database\ConsoleServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use JosBarbosa\ConsoleDbProfiler\Tests\Commands\Command01;
use JosBarbosa\ConsoleDbProfiler\Tests\Commands\Command02;
use JosBarbosa\ConsoleDbProfiler\Tests\Database\Test;

/**
 * Class TestCase
 * @package PackageTests
 */
class TestCase extends OrchestraTestCase
{
    /**
     * @var string
     */
    protected $defaultSql = 'select * from "tests"';

    /**
     * @var string
     */
    protected $defaultAlterSql = 'alter table "tests" modify column name text';

    /**
     * @var float
     */
    protected $queryTimeFast = 1.34;

    /**
     * @var float
     */
    protected $queryTimeMoreSlow = 2.56;

    /**
     * @var array
     */
    protected $commands = [
        Command01::class,
        Command02::class,
    ];

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom(realpath(__DIR__ . '/Database/migrations'));
        $this->withFactories(realpath(__DIR__ . '/Database/factories'));
        factory(Test::class, 10)->create();
    }

    /**
     * @inheritdoc
     */
    protected function getPackageProviders($app): array
    {
        Event::listen(ArtisanStarting::class, function (ArtisanStarting $event) {
            foreach ($this->commands as $command) {
                $event->artisan->resolve($command);
            }
        });

        return [
            ConsoleDbProfilerServiceProvider::class,
            ConsoleServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEnvironmentSetUp($app)
    {
        /** Setup default database to use sqlite :memory: **/
        $app['config']->set('database.default', 'testconsole');
        $app['config']->set('database.connections.testconsole', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageAliases($app): array
    {
        return [
            'config' => 'Illuminate\Config\Repository',
            'files'  => 'Illuminate\Filesystem\Filesystem',
        ];
    }

    /**
     * @param float $time
     * @param string|null $sql
     * @param bool $isDuplicate
     * @return Query
     */
    protected function query(float $time = null, string $sql = null, bool $isDuplicate = false): Query
    {
        return new Query(
            ($sql) ?: $this->defaultSql,
            ($time) ?: $this->queryTimeFast,
            'mysql',
            $isDuplicate
        );
    }
}
