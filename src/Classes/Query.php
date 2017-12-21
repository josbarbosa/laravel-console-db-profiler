<?php namespace JosBarbosa\ConsoleDbProfiler\Classes;

/**
 * Class Query
 * @package JosBarbosa\ConsoleDbProfiler\Classes
 */
class Query
{
    /**
     * @var string
     */
    protected $sql;

    /**
     * @var float
     */
    protected $time;

    /**
     * @var bool
     */
    protected $isDuplicate;

    /**
     * @var string
     */
    protected $connectionName;

    /**
     * Query constructor.
     * @param string $sql
     * @param float $time
     * @param string $connectionName
     * @param bool $isDuplicate
     */
    public function __construct(string $sql, float $time, string $connectionName, bool $isDuplicate = false)
    {
        $this->sql = $sql;
        $this->time = $time;
        $this->connectionName = $connectionName;
        $this->isDuplicate = $isDuplicate;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * @param string $sql
     */
    public function setSql(string $sql)
    {
        $this->sql = $sql;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @param float $time
     */
    public function setTime(float $time)
    {
        $this->time = $time;
    }

    /**
     * @return bool
     */
    public function isDuplicate(): bool
    {
        return $this->isDuplicate;
    }

    /**
     * @param bool $isDuplicate
     */
    public function setIsDuplicate(bool $isDuplicate)
    {
        $this->isDuplicate = $isDuplicate;
    }

    /**
     * @return string
     */
    public function getConnectionName(): string
    {
        return $this->connectionName;
    }

    /**
     * @param string $connectionName
     */
    public function setConnectionName(string $connectionName)
    {
        $this->connectionName = $connectionName;
    }
}
