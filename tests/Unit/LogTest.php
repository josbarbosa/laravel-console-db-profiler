<?php namespace JosBarbosa\ConsoleDbProfiler\Tests\Unit;

use JosBarbosa\ConsoleDbProfiler\Classes\Log;
use JosBarbosa\ConsoleDbProfiler\Tests\TestCase;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class LogTest
 * @package PackageTests\Unit
 */
class LogTest extends TestCase
{
    /** @test */
    function can_manage_a_query_log_file()
    {
        $path = h::getConfig('log.options.path');

        $log = new Log($path);

        $log->header('Test');

        $log->log('aaa');

        $this->assertFileExists($path);
        $this->assertTrue($log->exists());

        $fileContent = file_get_contents($path);

        $this->assertContains('Test', $fileContent);
        $this->assertContains('aaa', $fileContent);
    }
}
