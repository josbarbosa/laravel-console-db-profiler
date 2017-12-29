<?php namespace JosBarbosa\ConsoleDbProfiler\Outputs;

use JosBarbosa\ConsoleDbProfiler\Classes\Query;
use JosBarbosa\ConsoleDbProfiler\Collectors\Profile;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class OutputProfilingTable
 * @package JosBarbosa\ConsoleDbProfiler\Commands
 */
class OutputProfileTable extends OutputTable
{
    public function handle()
    {
        parent::handle();
        /** @var Profile $profiles */
        $profiles = $this->collector;
        (new OutputLimitMessage($profiles->getTotalQueries(), h::getConfig('limit')))->handle();
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        $profileTitleText = $this->textColorHighlight(h::trans('profiling'));
        $colProfileTitle = $this->tableCell($profileTitleText, 2);
        $colTimeTitle = $this->textColorHighlight(h::trans('time'));
        $colSqlTitle = $this->textColorHighlight(h::trans('query'));

        return [[$colProfileTitle], [$colTimeTitle, $colSqlTitle]];
    }

    /**
     * @return array
     */
    public function rows(): array
    {
        $rows = [];
        $limit = h::getConfig('limit');

        $this->collector->collection()->take($limit)->map(function (Query $query, int $i) use (&$rows) {
            $time = $query->getTime();
            $timeColumn = $this->textColorOk($time);

            if ($time >= h::getConfig('threshold.slow_query')) {
                $timeColumn = $this->error($time);
            }

            $sqlColumn = $this->wrapTextInColumn($query->getSql());

            if (h::getConfig('duplicates') && $query->isDuplicate()) {
                $sqlColumn = $this->error($sqlColumn);
            }

            /** @var bool $drawSeparator */
            $drawSeparator = ($i > 0);
            array_push($rows, $this->tableSeparator($drawSeparator), [$timeColumn, $sqlColumn]);
        });

        return $rows;
    }
}
