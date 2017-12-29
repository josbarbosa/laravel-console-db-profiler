<?php namespace JosBarbosa\ConsoleDbProfiler\Collectors;

use Illuminate\Support\Collection;

/**
 * Class Typology
 * @package JosBarbosa\ConsoleDbProfiler\Classes
 */
class Typology extends Collector
{
    /**
     * @param string $sql
     */
    public function collect($sql)
    {
        $removedRoundBrackets = str_replace('(', '', $sql);
        /**
         * Get the query typology (select, alter, delete, insert, ...)
         * @var string $typologyToken
         */
        $typologyToken = strtok($removedRoundBrackets, ' ');
        $typology = ['typology' => $typologyToken];
        parent::collect($typology);
    }

    /**
     * @return Collection
     */
    public function groupCountTypologies(): Collection
    {
        return $this->collector->groupBy('typology')->map(function (Collection $typologies) {
            return $typologies->count();
        });
    }
}
