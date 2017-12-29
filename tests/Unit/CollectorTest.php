<?php namespace JosBarbosa\ConsoleDbProfiler\Tests\Unit;

use Illuminate\Support\Collection;
use JosBarbosa\ConsoleDbProfiler\Collectors\Collector;
use JosBarbosa\ConsoleDbProfiler\Tests\TestCase;

/**
 * Class CollectorTest
 * @package PackageTests\Unit
 */
class CollectorTest extends TestCase
{
    /** @var Collector $collector */
    protected $collector;

    public function setUp()
    {
        parent::setUp();
        $this->collector = new Collector();
    }

    /** @test */
    function it_returns_a_collection()
    {
        $this->collector->collect("test");
        $this->assertInstanceOf(Collection::class, $this->collector->collection());
    }
}
