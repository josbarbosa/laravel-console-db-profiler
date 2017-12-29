<?php namespace JosBarbosa\ConsoleDbProfiler\Tests\Unit;

use JosBarbosa\ConsoleDbProfiler\Classes\Query;
use JosBarbosa\ConsoleDbProfiler\Tests\TestCase;

/**
 * Class QueryTest
 * @package PackageTests\Unit
 */
class QueryTest extends TestCase
{
    /** @var Query $query */
    protected $query;

    public function setUp()
    {
        parent::setUp();
        $this->query = $this->query();
    }

    /** @test */
    function it_get_set_query_property()
    {
        $this->query->setSql($this->defaultSql);
        $this->assertEquals($this->defaultSql, $this->query->getSql());
    }

    /** @test */
    function it_get_set_time_property()
    {
        $time = 15;
        $this->query->setTime($time);
        $this->assertEquals($time, $this->query->getTime());
    }

    /** @test */
    function it_get_set_connection_property()
    {
        $connection = "mysql_test";
        $this->query->setConnectionName($connection);
        $this->assertEquals($connection, $this->query->getConnectionName());
    }

    /** @test */
    function it_get_set_duplicate_property()
    {
        $this->query->setIsDuplicate(true);
        $this->assertTrue($this->query->isDuplicate());
    }
}
