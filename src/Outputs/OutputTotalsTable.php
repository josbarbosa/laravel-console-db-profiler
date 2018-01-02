<?php namespace JosBarbosa\ConsoleDbProfiler\Outputs;

use JosBarbosa\ConsoleDbProfiler\Collectors\Profile;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class OutputTotalsTable
 * @package JosBarbosa\ConsoleDbProfiler\Commands
 */
class OutputTotalsTable extends OutputTable
{
    /**
     * @return array
     */
    public function rows(): array
    {
        return [$this->totalTimeRow(), $this->totalDuplicatesRow()];
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function totalDuplicatesRow(): array
    {
        /** @var Profile $profiles */
        $profiles = $this->collector;
        $totalDuplicates = $profiles->getTotalDuplicates();

        if ($totalDuplicates) {
            $colDuplicates = $this->textColorWarning($totalDuplicates);
            $strPlural = str_plural(h::trans('duplicate'), $totalDuplicates);
            $colDuplicatesTitle = $strPlural . ' ' . h::trans('n1_problem');

            return [$colDuplicates, $colDuplicatesTitle];
        }

        return [];
    }

    /**
     * @return array
     */
    public function totalTimeRow(): array
    {
        /** @var Profile $profiles */
        $profiles = $this->collector;
        $totalTime = $profiles->getTotalTime();

        if ($totalTime) {
            $colThreshold = $this->coloredThreshold();
            $colThresholdTitle = h::trans('total_time');

            return [$colThreshold, $colThresholdTitle];
        }

        return [];
    }

    /**
     * @return string
     */
    public function coloredThreshold(): string
    {
        /** @var Profile $profiles */
        $profiles = $this->collector;
        $totalTime = $profiles->getTotalTime();
        $threshold = h::getConfig('threshold.total_queries');

        return ($totalTime > $threshold) ? $this->textColorWarning($totalTime) : $this->textColorOk($totalTime);
    }
}
