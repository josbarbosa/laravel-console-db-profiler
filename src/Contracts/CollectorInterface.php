<?php namespace JosBarbosa\ConsoleDbProfiler\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface CollectorInterface
 * @package JosBarbosa\ConsoleDbProfiler\Contracts
 */
interface CollectorInterface
{
    /**
     * @param $item
     */
    public function collect($item): void;

    /**
     * @return Collection
     */
    public function collection(): Collection;

    /**
     * @return bool
     */
    public function hasItems(): bool;
}
