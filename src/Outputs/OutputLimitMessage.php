<?php namespace JosBarbosa\ConsoleDbProfiler\Outputs;

use JosBarbosa\ConsoleDbProfiler\Traits\Printable;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class OutputLimitMessage
 * @package JosBarbosa\ConsoleDbProfiler\Commands
 */
class OutputLimitMessage
{
    use Printable;
    /**
     * @var int
     */
    protected $totalRows;
    /**
     * @var int
     */
    protected $limitRows;

    /**
     * OutputLimitMessage constructor.
     * @param int $totalRows
     * @param int $limitRows
     */
    public function __construct(int $totalRows, int $limitRows)
    {
        $this->totalRows = $totalRows;
        $this->limitRows = $limitRows;
    }

    public function handle(): void
    {
        if ($this->totalRows > $this->limitRows) {
            $this->line('');
            $this->line(h::trans(
                'limit_rows',
                [
                    'LIMIT_ROWS' => $this->highlight($this->limitRows),
                    'TOTAL_ROWS' => $this->highlight($this->totalRows),
                ]
            ));
            $this->line(h::trans('more_rows'));
            $this->line('');
        }
    }
}
