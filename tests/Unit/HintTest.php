<?php namespace JosBarbosa\ConsoleDbProfiler\Tests\Unit;

use JosBarbosa\ConsoleDbProfiler\Collectors\Hint;
use JosBarbosa\ConsoleDbProfiler\Tests\TestCase;

/**
 * Class HintTest
 * @package PackageTests\Unit
 */
class HintTest extends TestCase
{
    /**
     * @var array
     */
    protected $queries = [
        "select * from tests order by rand()",
        "select * from tests limit 100",
        "select * from tests where name != 'name'",
        "select * from tests where name like '%a%'",
    ];

    /**
     * @var Hint $hints
     */
    protected $hints;

    protected function setUp()
    {
        parent::setUp();
        $this->hints = new Hint();
    }

    /** @test */
    function it_collects_hints()
    {
        foreach ($this->queries as $query) {
            $this->assertHints($query);
        }
    }

    /** @test */
    function it_tests_missing_where_clause()
    {
        $expression = $this->hints->getRegularExpressions()['where_clause_not_exists'];

        /**
         * array with random queries to test if where is missing in a select
         * false: where is present
         * true: where is missing
         */
        $where = [
            "select * from tests where a=1"                                                                    => false,
            "select a from tests"                                                                              => true,
            "(select a from tests where a=1) union (select b from tests)"                                      => false,
            "(select a, (select c from tests limit 1) from t where a=1"                                        => false,
            "(select a from t where a= 1) union (select a from y) union (select u from i where u=1)"           => false,
            "(select a from t where a= 1) union (select a from y where a=2) union (select u from i where u=1)" => false,
            "(select a from tests a=1) union (select b from tests b=3)"                                        => true,
        ];

        $this->assertRegExpressions($expression, $where);
    }

    /** @test */
    function it_tests_if_the_correct_operator_is_present()
    {
        $expression = $this->hints->getRegularExpressions()['wrong_not_equal_operator_exists'];

        $operator = [
            "select a from tests where a!=2"                                 => true,
            "select a from tests where a<>2"                                 => false,
            "select a, (select u from t where u != 2) from tests where a<>2" => true,
        ];

        $this->assertRegExpressions($expression, $operator);
    }

    /** @test */
    function it_tests_if_a_select_asterisk_is_present()
    {
        $expression = $this->hints->getRegularExpressions()['select_has_an_asterisk'];

        $select = [
            "select * from tests where a=2"       => true,
            "select a.* from tests where a=2"     => true,
            "select a from tests where a<>2"      => false,
            "select a, b.* from tests where a<>2" => false,
        ];

        $this->assertRegExpressions($expression, $select);
    }

    /** @test */
    function it_tests_if_a_order_rand_is_present()
    {
        $expression = $this->hints->getRegularExpressions()['order_by_random'];

        $orderRand = [
            "select * from tests where a=2 order by rand()" => true,
            "select a from tests where a<>2"                => false,
        ];

        $this->assertRegExpressions($expression, $orderRand);
    }

    /** @test */
    function it_tests_if_order_by_is_missing_in_a_select_with_a_limit()
    {
        $expression = $this->hints->getRegularExpressions()['limit_without_order_by'];

        /**
         * array with random queries to test if order by is missing in a select
         * false: order by is present
         * true: order by is missing
         */
        $limit = [
            "select a, b from tests where a=2 order by a limit 1"            => false,
            "select a, b from tests where a<>2 limit 1"                      => true,
            "(select a, (select h from t limit 1) from tests where a<>2 order by c limit 1) "
            . "union (select a, b from tests where a<>2 order by r limit 1)" => true,
            "(select a, b from tests where a<>2 order by c limit 1) "
            . "union (select a, b from tests where a<>2 limit 1)"            => true,
            "(select a, (select h from t order by h limit 1) from tests where a<>2 order by c limit 1) "
            . "union (select a, b from tests where a<>2 order by r limit 1)" => false,
        ];

        $this->assertRegExpressions($expression, $limit);
    }

    /** @test */
    function it_tests_if_a_leading_wildcard_operator_is_present()
    {
        $expression = $this->hints->getRegularExpressions()['like_clause_with_prefix_wildcard'];

        $like = [
            "select a from b where a like '%a'"                                             => true,
            "select a from b where a like 'a%'"                                             => false,
            "select a from b where a like '%a%'"                                            => true,
            "(select a from b where a like 'a%') union (select a from c where a like '%a')" => true,
            "(select a, (select b from t where b like 'b%') from b where a like 'a%')"      => false,

        ];

        $this->assertRegExpressions($expression, $like);
    }

    /**
     * @param string $expression
     * @param array $testQueries
     */
    function assertRegExpressions(string $expression, array $testQueries)
    {
        foreach ($testQueries as $regExp => $isMatching) {
            if ($isMatching) {
                $this->assertRegExp($expression, $regExp);
            } else {
                $this->assertNotRegExp($expression, $regExp);
            }
        }
    }

    /**
     * @param string $sql
     */
    function assertHints(string $sql)
    {
        $hintCollect = new Hint();
        $hintCollect->collect($sql);
        $hints = $hintCollect->queryAnalysis($sql);
        $this->assertEquals($hints, $hintCollect->collection()->toArray());
    }
}
