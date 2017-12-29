<?php namespace JosBarbosa\ConsoleDbProfiler\Collectors;

use Illuminate\Support\Collection;
use JosBarbosa\ConsoleDbProfiler\Contracts\CollectorInterface;

/**
 * Class Collector
 * @package JosBarbosa\ConsoleDbProfiler\Classes
 */
class Collector implements CollectorInterface
{
    /**
     * @var Collection
     */
    protected $collector;

    /**
     * Collector constructor.
     */
    public function __construct()
    {
        $this->collector = collect();
    }

    /**
     * @param $item
     */
    public function collect($item)
    {
        $this->collector->push($item);
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->collector;
    }
}
