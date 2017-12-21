<?php namespace PackageTests\Unit;

use Illuminate\Support\Collection;
use JosBarbosa\ConsoleDbProfiler\Collectors\Collector;
use PackageTests\TestCase;

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
    function its_empty(): void
    {
        $this->assertFalse($this->collector->hasItems());
    }

    /** @test */
    function it_has_items(): void
    {
        $this->collector->collect("test");
        $this->assertTrue($this->collector->hasItems());

    }

    /** @test */
    function it_returns_a_collection(): void
    {
        $this->collector->collect("test");
        $this->assertInstanceOf(Collection::class, $this->collector->collection());
    }
}
