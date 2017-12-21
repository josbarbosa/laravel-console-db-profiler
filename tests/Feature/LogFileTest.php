<?php namespace PackageTests\Feature;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;
use PackageTests\Database\Test;
use PackageTests\TestCaseConsole;

/**
 * Class LogFileTest
 * @package PackageTests\Feature
 */
class LogFileTest extends TestCaseConsole
{
    /**
     * @var string
     */
    protected $path = '';

    function setUp()
    {
        parent::setUp();
        $this->path = h::getConfig('log.options.path');
        File::delete($this->path);
        $this->setEnv('local');
    }

    /** @test */
    function it_enables_query_log(): void
    {
        $this->setConfig('log.enabled', true)->boot();

        Test::count();

        $this->assertFileExists($this->path);
    }

    /** @test */
    function it_disables_query_log(): void
    {
        $this->setConfig('log.enabled', false)->boot();

        Test::count();

        $this->assertFileNotExists($this->path);
    }

    /** @test */
    function it_saves_the_log_file_in_a_new_path_location(): void
    {
        $newPath = storage_path('app/query.log');

        $this->setConfig('log.enabled', true)->setConfig('log.options.path', $newPath)->boot();

        Test::count();

        $this->assertFileExists($newPath);

        File::delete($newPath);
    }

    /** @test */
    function it_appends_queries_to_the_log_file(): void
    {
        $this->setConfig('log.enabled', true)->boot();

        Test::count();

        DB::getEventDispatcher()->forget(QueryExecuted::class);

        $this->setConfig('log.options.append', true)->boot();

        Test::all();

        $fileContent = file_get_contents($this->path);

        $this->assertContains('select count(*) as aggregate from "tests"', $fileContent);
        $this->assertContains($this->defaultSql, $fileContent);
    }

    /** @test */
    function it_deletes_the_log_file_on_every_execution(): void
    {
        $this->setConfig('log.options.append', false)->setConfig('log.enabled', true)->boot();

        Test::count();

        DB::getEventDispatcher()->forget(QueryExecuted::class);

        $this->boot();

        Test::all();

        $fileContent = file_get_contents($this->path);

        $this->assertNotContains('select count(*) as aggregate from "tests"', $fileContent);
        $this->assertContains($this->defaultSql, $fileContent);
    }
}
