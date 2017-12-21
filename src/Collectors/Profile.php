<?php namespace JosBarbosa\ConsoleDbProfiler\Collectors;

use JosBarbosa\ConsoleDbProfiler\Classes\Query;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class Profile
 * @package JosBarbosa\ConsoleDbProfiler\Classes
 */
class Profile extends Collector
{
    /**
     * @var float
     */
    protected $totalTime = 0.0;

    /**
     * @var int
     */
    protected $totalDuplicates = 0;

    /**
     * @param Query $query
     */
    public function collect($query)
    {
        if (h::getConfig('total')) {
            $this->addTime($query->getTime());
        }

        if ($this->containsDuplicates($query) && h::getConfig('duplicates')) {
            $query->setIsDuplicate(true);
            $this->incrementTotalDuplicates();
        }

        parent::collect($query);
    }

    /**
     * @param float $time
     */
    protected function addTime(float $time)
    {
        $this->totalTime += $time;
    }

    /**
     * @param Query $query
     * @return bool
     */
    public function containsDuplicates(Query $query): bool
    {
        return $this->collection()->contains(function (Query $profile) use ($query) {

            if ($contains = $profile->getSql() === $query->getSql()) {
                ($profile->isDuplicate()) ?: $profile->setIsDuplicate(true);
            }

            return $contains;
        });
    }

    /**
     * Increment Total Duplicates
     */
    protected function incrementTotalDuplicates()
    {
        $this->totalDuplicates += 1;
    }

    /**
     * @return int
     */
    public function getTotalQueries(): int
    {
        return $this->collector->count();
    }

    /**
     * @return float
     */
    public function getTotalTime(): float
    {
        return $this->totalTime;
    }

    /**
     * @return int
     */
    public function getTotalDuplicates(): int
    {
        return $this->totalDuplicates;
    }
}
