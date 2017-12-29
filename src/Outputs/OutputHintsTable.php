<?php namespace JosBarbosa\ConsoleDbProfiler\Outputs;

use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class OutputHintsTable
 * @package JosBarbosa\ConsoleDbProfiler\Commands
 */
class OutputHintsTable extends OutputTable
{
    /**
     * @return array
     */
    public function headers(): array
    {
        return [$this->textColorHighlight(h::trans('hints'))];
    }

    /**
     * @return array
     */
    public function rows(): array
    {
        return $this->collector
            ->collection()
            ->transform(function (string $hint) {
                return [$this->wrapTextInColumn($hint)];
            })
            ->toArray();
    }
}
