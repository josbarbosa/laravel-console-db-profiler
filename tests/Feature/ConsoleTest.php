<?php namespace PackageTests\Feature;

use Carbon\Carbon;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use JosBarbosa\ConsoleDbProfiler\ConsoleDbProfiler;
use PackageTests\Database\Test;
use PackageTests\TestCaseConsole;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class ConsoleTest
 * @package PackageTests\Feature
 */
class ConsoleTest extends TestCaseConsole
{
    use RefreshDatabase;

    /** @test */
    function it_builds_the_console_db_profiling_with_all_configs_disabled()
    {
        /** set the env where the test will run */
        $this->setEnv('local')
            ->setConfig('hints', false)
            ->setConfig('typologies', false)
            ->setConfig('total', false)
            ->setConfig('duplicates', false)
            ->boot();

        /** run queries */
        DB::enableQueryLog();
        Test::count();

        $time = $this->executionTime(DB::getQueryLog());
        $this->application->terminate();

        $title = h::trans('profiling');
        $titleTime = h::trans('time');
        $titleQuery = h::trans('query');

        $table = <<<TABLE
+---------------+-------------------------------------------+
| {$title}                                 |
+---------------+-------------------------------------------+
| {$titleTime}     | {$titleQuery}                                     |
+---------------+-------------------------------------------+
| {$time[0]}| select count(*) as aggregate from "tests" |
+---------------+-------------------------------------------+

TABLE;
        $this->assertContains($table, $this->output->output);
        $this->assertNotContains(h::trans('total_time'), $this->output->output);
        $this->assertNotContains(h::trans('n1_problem'), $this->output->output);
        $this->assertNotContains(h::trans('typologies'), $this->output->output);
        $this->assertNotContains(h::trans('hints'), $this->output->output);
    }

    /** @test */
    function it_builds_the_console_db_profiling_with_all_configs_enabled()
    {
        $this->setEnv('local')
            ->setConfig('hints', true)
            ->setConfig('typologies', true)
            ->setConfig('total', true)
            ->setConfig('duplicates', true)
            ->boot();

        DB::enableQueryLog();
        Test::count();
        Test::count();
        $time = $this->executionTime(DB::getQueryLog());

        $this->application->terminate();
        $title = h::trans('profiling');
        $titleTime = h::trans('time');
        $titleQuery = h::trans('query');

        $table = <<<TABLE
+---------------+-------------------------------------------+
| {$title}                                 |
+---------------+-------------------------------------------+
| {$titleTime}     | {$titleQuery}                                     |
+---------------+-------------------------------------------+
| {$time[0]}| select count(*) as aggregate from "tests" |
+---------------+-------------------------------------------+
| {$time[1]}| select count(*) as aggregate from "tests" |
+---------------+-------------------------------------------+

TABLE;
        $this->assertContains($table, $this->output->output);
        $this->assertContains(h::trans('total_time'), $this->output->output);
        $this->assertContains(h::trans('n1_problem'), $this->output->output);
        $this->assertContains(h::trans('typologies'), $this->output->output);
        $this->assertContains(h::trans('hints'), $this->output->output);
    }

    /** @test */
    function it_not_runs_from_console()
    {
        $this->setEnv('local')->runningInConsole(false)->boot();

        $this->assertFalse(DB::getEventDispatcher()->hasListeners(QueryExecuted::class));
    }

    /** @test */
    function it_disable_profiler()
    {
        h::setConfig('enabled', false);
        $this->setEnv('local')->boot();

        $this->assertFalse(DB::getEventDispatcher()->hasListeners(QueryExecuted::class));
    }

    /** @test */
    function it_enable_profiler_using_debug_mode_option_vvv()
    {
        array_push($_SERVER['argv'], '-vvv');

        h::setConfig('enabled', false);
        $this->setEnv('local')->boot();

        array_pop($_SERVER['argv']);

        $this->assertTrue(DB::getEventDispatcher()->hasListeners(QueryExecuted::class));
    }

    /** @test */
    function it_enable_profiler_for_an_environment_that_its_not_local()
    {
        h::setConfig('environment.production', true);
        $this->setEnv('production')->boot();

        $this->assertTrue(DB::getEventDispatcher()->hasListeners(QueryExecuted::class));
    }

    /** @test */
    function it_tests_a_query_with_a_carbon_date()
    {
        $this->setEnv('local')->boot();

        DB::enableQueryLog();
        Test::where('created_at', '>=', Carbon::now())->get();

        $this->application->terminate();

        $query = DB::getQueryLog()[0]['query'];
        $bindings = DB::getQueryLog()[0]['bindings'];
        $this->assertContains(
            (new ConsoleDbProfiler($this->app))->applyBindings($query, $bindings),
            $this->output->output
        );
    }

    /** @test */
    function it_tests_an_artisan_command()
    {
        $this->setEnv('local')->boot();

        Artisan::call("test:command01");

        $this->application->terminate();

        $this->assertContains($this->defaultSql, $this->output->output);
    }

    /** @test */
    function it_tests_multiple_command_calls()
    {
        $this->setEnv('local')->boot();

        Artisan::call("test:command02");

        $this->application->terminate();

        $this->assertContains($this->defaultSql, $this->output->output);
        $this->assertContains('select count(*) as aggregate from "tests"', $this->output->output);
    }

    /**
     * @param array $queryLog
     * @return array
     */
    function executionTime(array $queryLog): array
    {
        $sqlLog = [];
        foreach ($queryLog as $log) {
            $sqlLog[] = $log['time'] . str_pad(" ", 14 - strlen($log['time'] ?? ''));
        }
        return $sqlLog;
    }
}
