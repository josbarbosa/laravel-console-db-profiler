<?php namespace JosBarbosa\ConsoleDbProfiler\Outputs;

use JosBarbosa\ConsoleDbProfiler\Collectors\Typology;
use JosBarbosa\ConsoleDbProfiler\Helpers\Helper as h;

/**
 * Class OutputTypologiesTable
 * @package JosBarbosa\ConsoleDbProfiler\Commands
 */
class OutputTypologiesTable extends OutputTable
{
    /**
     * @return array
     */
    public function headers(): array
    {
        $colMainTitle = $this->tableCell($this->textColorHighlight(h::trans('typologies')), 2);
        $colRunTitle = $this->textColorHighlight(h::trans('run'));
        $colTypologyTitle = $this->textColorHighlight(h::trans('typology'));

        return [[$colMainTitle], [$colRunTitle, $colTypologyTitle]];
    }

    /**
     * @return array
     */
    public function rows(): array
    {
        /** @var Typology $typologies */
        $typologies = $this->collector;
        return $typologies->groupCountTypologies()->map(function (int $count, string $typology) {
            return [$count, h::strPlural($typology, $count)];
        })->toArray();
    }
}
