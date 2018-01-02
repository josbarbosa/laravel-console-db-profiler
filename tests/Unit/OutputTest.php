<?php namespace JosBarbosa\ConsoleDbProfiler\Tests\Unit;

use JosBarbosa\ConsoleDbProfiler\Collectors\Hint;
use JosBarbosa\ConsoleDbProfiler\Collectors\Profile;
use JosBarbosa\ConsoleDbProfiler\Collectors\Typology;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputHintsTable;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputLimitMessage;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputProfileTable;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputTable;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputTypologiesTable;
use JosBarbosa\ConsoleDbProfiler\Outputs\OutputTotalsTable;
use JosBarbosa\ConsoleDbProfiler\ConsoleDbProfiler;
use JosBarbosa\ConsoleDbProfiler\Traits\Printable;
use JosBarbosa\ConsoleDbProfiler\Tests\TestCase;
use Symfony\Component\Console\Helper\TableSeparator;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;
use Mockery as m;

/**
 * Class OutputTest
 * @package PackageTests\Unit
 */
class OutputTest extends TestCase
{
    use Printable;

    /** @var Profile $profiles */
    protected $profiles;
    /** @var Hint $hints */
    protected $hints;
    /** @var Typology $typologies */
    protected $typologies;

    public function setUp()
    {
        parent::setUp();

        $this->stub();
    }

    /** @test */
    function it_tests_the_concrete_method_of_the_output_table_abstract_class()
    {
        $mock = m::mock(OutputTable::class, [$this->profiles])->shouldAllowMockingProtectedMethods();
        $mock->shouldReceive('rows')->once()->withNoArgs()->andReturn(['row']);
        $mock->shouldReceive('headers')->once()->withNoArgs()->andReturn(['header']);
        $mock->shouldReceive('buildTable')->once()->withArgs([['header'], ['row']]);
        $mock->shouldReceive('handle')->once()->withNoArgs()->passthru();
        $mock->handle();
    }

    /** @test */
    function its_ready_to_output_hints_to_the_console()
    {
        $rowsOutput = [
            [
                h::trans('where_clause_not_exists'),
            ],
            [
                h::trans('select_has_an_asterisk'),
            ],
        ];

        $headersOutput = [
            $this->textColorHighlight(h::trans('hints')),
        ];

        $outputHintsTable = new OutputHintsTable($this->hints);
        $this->assertEquals($rowsOutput, $outputHintsTable->rows());
        $this->assertEquals($headersOutput, $outputHintsTable->headers());
    }

    /** @test */
    function its_ready_to_output_a_limit_message_to_the_console()
    {
        $totalRows = 10;
        $limitRows = 5;
        $mock = m::mock(OutputLimitMessage::class, [$totalRows, $limitRows])->makePartial();
        $mock->shouldReceive('handle')->once()->withNoArgs()->passthru();
        $mock->shouldReceive('line')->times(2)->withArgs(['']);
        $mock->shouldReceive('line')->times(1)->withArgs([h::trans('more_rows')]);
        $mock->shouldReceive('line')->times(1)->withArgs([
            h::trans(
                'limit_rows',
                [
                    'LIMIT_ROWS' => $this->highlight($limitRows),
                    'TOTAL_ROWS' => $this->highlight($totalRows),
                ]
            ),
        ]);
        $mock->handle();
    }

    /** @test */
    function its_not_ready_to_output_a_message_limit_to_the_console()
    {
        $totalRows = 10;
        $limitRows = 50;
        $mock = m::mock(OutputLimitMessage::class, [$totalRows, $limitRows])->makePartial();
        $mock->shouldReceive('handle')->once()->withNoArgs()->passthru();
        $mock->shouldReceive('line')->never();
        $mock->handle();
    }

    /** @test */
    function its_ready_to_output_profiles_to_the_console()
    {
        $rowsOutput = [
            [],
            [
                $this->textColorOk($this->queryTimeFast),
                $this->defaultSql,
            ],
            new TableSeparator(),
            [
                $this->textColorOk($this->queryTimeMoreSlow),
                $this->defaultSql . " where name='new'",
            ],

        ];

        $profileTitleText = $this->textColorHighlight(h::trans('profiling'));
        $headersOutput = [
            [
                $this->tableCell($profileTitleText, 2),
            ],
            [
                $this->textColorHighlight(h::trans('time')),
                $this->textColorHighlight(h::trans('query')),
            ],
        ];

        $outputProfileTable = new OutputProfileTable($this->profiles);
        $this->assertEquals($rowsOutput, $outputProfileTable->rows());
        $this->assertEquals($headersOutput, $outputProfileTable->headers());
    }

    /** @test */
    function its_ready_to_output_profiles_with_warnings_to_the_console()
    {
        $slowQuery = 30.5;
        /** Test all methods with warnings */
        h::setConfig('threshold.slow_query', ($this->queryTimeFast + 0.1));
        $this->profiles->collect($this->query($slowQuery));

        $rowsOutput = [
            [],
            [
                $this->textColorOk($this->queryTimeFast),
                $this->warning($this->defaultSql),
            ],
            new TableSeparator(),
            [
                $this->warning($this->queryTimeMoreSlow),
                $this->defaultSql . " where name='new'",
            ],
            new TableSeparator(),
            [
                $this->warning($slowQuery),
                $this->warning($this->defaultSql),
            ],
        ];
        $this->assertEquals($rowsOutput, (new OutputProfileTable($this->profiles))->rows());
    }

    /** @test */
    function its_ready_to_output_typologies_to_the_console()
    {
        $rowsOutput = [
            'select' => [
                2,
                'selects',
            ],
            'alter'  => [
                1,
                'alter',
            ],
        ];

        $headersOutput = [
            [
                $this->tableCell($this->textColorHighlight(h::trans('typologies')), 2),
            ],
            [
                $this->textColorHighlight(h::trans('run')),
                $this->textColorHighlight(h::trans('typology')),
            ],
        ];

        $outputTypologies = new OutputTypologiesTable($this->typologies);
        $this->assertEquals($rowsOutput, $outputTypologies->rows());
        $this->assertEquals($headersOutput, $outputTypologies->headers());
    }

    /** @test */
    function its_ready_to_output_totals_to_the_console()
    {
        $profiles = $this->profiles;
        $profiles->collect($this->query());
        $outputTotalsTable = new OutputTotalsTable($profiles);

        $this->assertEmpty($outputTotalsTable->headers());

        $this->assertEquals(
            [
                [
                    $this->textColorOk($profiles->getTotalTime()),
                    h::trans('total_time'),
                ],
                [
                    $this->textColorWarning($profiles->getTotalDuplicates()),
                    str_plural(h::trans('duplicate'), $profiles->getTotalDuplicates()) . ' ' . h::trans('n1_problem'),
                ],
            ],
            $outputTotalsTable->rows()
        );
    }

    /** @test */
    function it_tests_the_total_queries_time()
    {
        $profiles = $this->profiles;
        $totalTime = $profiles->getTotalTime();

        $outputTotalsTable = new OutputTotalsTable($profiles);

        $this->assertEquals(
            [
                $this->textColorOk($totalTime),
                h::trans('total_time'),
            ],
            $outputTotalsTable->totalTimeRow()
        );

        /** Mock empty total time */
        $mockedProfile = m::mock($profiles)->makePartial();
        $mockedProfile->shouldReceive('getTotalTime')->once()->withNoArgs()->andReturn(0.0);

        $outputTotalsTable = new OutputTotalsTable($mockedProfile);
        $this->assertEmpty($outputTotalsTable->totalTimeRow());
    }

    /** @test */
    function it_tests_duplicate_queries()
    {
        $profiles = $this->profiles;
        $profiles->collect($this->query());
        $duplicates = $profiles->getTotalDuplicates();

        $outputTotalsTable = new OutputTotalsTable($profiles);

        $this->assertEquals(
            [
                $this->textColorWarning($duplicates),
                str_plural(h::trans('duplicate'), $duplicates) . ' ' . h::trans('n1_problem'),
            ],
            $outputTotalsTable->totalDuplicatesRow()
        );

        /** Mock empty duplicates */
        $mockedProfile = m::mock($profiles)->makePartial();
        $mockedProfile->shouldReceive('getTotalDuplicates')->once()->withNoArgs()->andReturn(0);

        $outputTotalsTable = new OutputTotalsTable($mockedProfile);
        $this->assertEmpty($outputTotalsTable->totalDuplicatesRow());
    }

    /** @test */
    function its_ready_to_output_queries_total_time_with_the_threshold_limit_exceeded_to_the_console()
    {
        h::setConfig('threshold.total_queries', -1);
        $profiles = $this->profiles;
        $totalTime = $profiles->getTotalTime();

        $outputTotalsTable = new OutputTotalsTable($profiles);

        $this->assertEquals(
            [
                $this->textColorWarning($totalTime),
                h::trans('total_time'),
            ],
            $outputTotalsTable->totalTimeRow()
        );
    }

    /** @test */
    function it_tests_thresholds()
    {
        $totalTime = $this->profiles->getTotalTime();
        $outputTotalsTable = new OutputTotalsTable($this->profiles);

        /** Test without warnings */
        $this->assertEquals($this->textColorOk($totalTime), $outputTotalsTable->coloredThreshold());

        /** Test with warnings */
        h::setConfig('threshold.total_queries', -1);
        $this->assertEquals($this->textColorWarning($totalTime), $outputTotalsTable->coloredThreshold());
    }

    protected function stub()
    {
        $sql = (new ConsoleDbProfiler($this->app))->applyBindings($this->defaultSql . ' where name=?', ['new']);
        $this->profiles = new Profile();
        $this->profiles->collect($this->query());
        $this->profiles->collect($this->query($this->queryTimeMoreSlow, $sql));
        $this->hints = new Hint();
        $this->hints->collect($this->defaultSql);
        $this->hints->collect($sql);
        $this->typologies = new Typology();
        $this->typologies->collect($this->defaultSql);
        $this->typologies->collect($sql);
        $this->typologies->collect($this->defaultAlterSql);
    }
}
