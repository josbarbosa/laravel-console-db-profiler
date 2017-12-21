<?php namespace PackageTests\Unit;

use JosBarbosa\ConsoleDbProfiler\Traits\Printable;
use PackageTests\TestCaseConsole;
use Symfony\Component\Console\Helper\TableSeparator;

/**
 * Class PrintableConsoleTest
 * @package PackageTests\Unit
 */
class PrintableConsoleTest extends TestCaseConsole
{
    use Printable;

    /** @test */
    function it_writes_a_raw_message()
    {
        $this->line('a');
        $this->assertEquals("a\n", $this->output->output);
    }

    /** @test */
    function it_outputs_nothing_when_trying_to_build_a_console_table_without_rows()
    {
        $this->buildTable(['a'], []);
        $this->assertEmpty($this->output->output);
    }

    /** @test */
    function it_outputs_a_console_table()
    {
        $this->buildTable([
            'column1',
            'column2',
        ], [
            [
                'content1 column1',
                'content1 column2',
            ],
            new TableSeparator(),
            [
                'content2 column1',
                'content2 column2',
            ],
        ]);
        $table = <<<'TABLE'
+------------------+------------------+
| column1          | column2          |
+------------------+------------------+
| content1 column1 | content1 column2 |
+------------------+------------------+
| content2 column1 | content2 column2 |
+------------------+------------------+

TABLE;
        $this->assertEquals($table, $this->output->output);
    }
}
