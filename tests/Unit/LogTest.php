<?php namespace PackageTests\Unit;

use JosBarbosa\ConsoleDbProfiler\Classes\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PackageTests\TestCase;
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
        $logger = new Logger('profiler_log');
        $lineFormatter = (new LineFormatter("%message% %context% %extra%\n", null, true, true));
        $streamHandler = (new StreamHandler($path))->setFormatter($lineFormatter);
        $logger->pushHandler($streamHandler);

        $queryLog = new Log(
            $logger,
            Logger::DEBUG
        );

        $queryLog->header('Test');

        $queryLog->write('aaa');

        $this->assertFileExists($path);

        $fileContent = file_get_contents($path);

        $this->assertContains('Test', $fileContent);
        $this->assertContains('aaa', $fileContent);
    }
}
