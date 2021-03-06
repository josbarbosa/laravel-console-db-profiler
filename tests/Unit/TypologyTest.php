<?php namespace JosBarbosa\ConsoleDbProfiler\Tests\Unit;

use JosBarbosa\ConsoleDbProfiler\Collectors\Typology;
use JosBarbosa\ConsoleDbProfiler\Tests\TestCase;

/**
 * Class TypologyTest
 * @package PackageTests\Unit
 */
class TypologyTest extends TestCase
{
    /** @var Typology $typology */
    protected $typology;

    public function setUp()
    {
        parent::setUp();
        $this->typology = new Typology();
    }

    /** @test */
    function it_collects_typologies()
    {
        $this->typology->collect($this->defaultSql);
        $this->assertEquals(['typology' => 'select'], $this->typology->collection()->first());
    }

    /** @test */
    function it_counts_typologies()
    {
        $this->addTypologies($this->defaultSql);
        $number = 2;
        $this->addTypologies($this->defaultAlterSql, $number);
        $this->assertEquals(['select' => 1, 'alter' => $number], $this->typology->groupCountTypologies()->toArray());
    }

    /**
     * @param string $sql
     * @param int $number
     */
    function addTypologies(string $sql, int $number = 1)
    {
        for ($i = 1; $i <= $number; $i++) {
            $this->typology->collect($sql);
        }
    }
}
