<?php namespace JosBarbosa\ConsoleDbProfiler\Outputs;

use JosBarbosa\ConsoleDbProfiler\Contracts\CollectorInterface;
use JosBarbosa\ConsoleDbProfiler\Traits\Printable;

/**
 * Class Output
 * @package JosBarbosa\ConsoleDbProfiler\Commands
 */
abstract class OutputTable
{
    use Printable;

    /**
     * @var CollectorInterface
     */
    protected $collector;

    /**
     * OutputTable constructor.
     * @param CollectorInterface $collector
     */
    public function __construct(CollectorInterface $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Handle the table output
     */
    final public function handle(): void
    {
        $this->buildTable($this->headers(), $this->rows());
    }

    /**
     * @return array
     */
    abstract protected function headers(): array;

    /**
     * @return array
     */
    abstract protected function rows(): array;
}
