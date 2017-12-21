<?php namespace JosBarbosa\ConsoleDbProfiler\Collectors;

use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class Hint
 * @package JosBarbosa\ConsoleDbProfiler\Classes
 */
class Hint extends Collector
{
    /**
     * @var array
     */
    protected $regularExpressions = [
        'wrong_not_equal_operator_exists'  => '/!=/i',
        'where_clause_not_exists'          => '/^\\s*\\(*\\s*select(?:(?!\\swhere\\s).)*\\z/i',
        'select_has_an_asterisk'           => '/^\\s*SELECT\\s*`?[a-zA-Z0-9]*`?\\.?\\*/i',
        'order_by_random'                  => '/order by rand()/i',
        'limit_without_order_by'           => '/\\sfrom\\s(?:(?!\\sorder\\sby\\s).)*\\slimit\\s/i',
        'like_clause_with_prefix_wildcard' => '/like\\s[\'"](%.*?)[\'"]/i',
    ];

    /**
     * @param string $sql
     */
    public function collect($sql): void
    {
        foreach ($this->queryAnalysis($sql) as $hint) {
            if (!$this->collection()->contains($hint)) {
                parent::collect($hint);
            }
        }
    }

    /**
     * Perform a simple regex query analysis
     *
     * @param string $sql
     * @return array
     */
    public function queryAnalysis(string $sql): array
    {
        $hints = [];

        foreach ($this->getRegularExpressions() as $translationKey => $expression) {
            if (preg_match($expression, $sql, $matches)) {
                $hints[] = h::trans($translationKey, ['MATCH' => $matches[1] ?? '']);
            }
        }

        return $hints;
    }

    /**
     * @return array
     */
    public function getRegularExpressions(): array
    {
        return $this->regularExpressions;
    }
}
