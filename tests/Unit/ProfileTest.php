<?php namespace PackageTests\Unit;

use JosBarbosa\ConsoleDbProfiler\Collectors\Profile;
use JosBarbosa\ConsoleDbProfiler\Classes\Query;
use PackageTests\TestCase;

/**
 * Class ProfileTest
 * @package PackageTests\Unit
 */
class ProfileTest extends TestCase
{
    /**
     * @var Profile $profile
     */
    protected $profile;
    /**
     * @var Query $query
     */
    protected $query;

    /**
     * Override parent setUp method
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->profile = new Profile();
        $this->query = $this->query(15);
        $this->collectsQuery(1);
    }

    /** @test */
    function it_collects_queries(): void
    {
        $this->assertEquals(0, $this->profile->getTotalDuplicates());
        $this->assertEquals(1, $this->profile->getTotalQueries());
        $this->assertEquals(15, $this->profile->getTotalTime());
    }

    /** @test */
    function it_collects_duplicate_queries(): void
    {
        $this->collectsQuery(1);
        $this->assertEquals(1, $this->profile->getTotalDuplicates());
        $this->assertEquals(2, $this->profile->getTotalQueries());
        $this->assertEquals(30, $this->profile->getTotalTime());
    }

    /**
     * @param int $number
     * @param Query|null $query
     */
    function collectsQuery(int $number, Query $query = null): void
    {
        for ($i = 1; $i <= $number; $i++) {
            $this->profile->collect(($query ?? $this->query));
        }
    }
}
