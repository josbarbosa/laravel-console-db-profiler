<?php namespace JosBarbosa\ConsoleDbProfiler\Classes;

use Psr\Log\LoggerInterface;

/**
 * Class Log
 * @package JosBarbosa\ConsoleDbProfiler\Classes
 */
class Log
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var int
     */
    protected $level;

    /**
     * Log constructor.
     * @param LoggerInterface $logger
     * @param int $level
     */
    public function __construct(LoggerInterface $logger, int $level)
    {
        $this->logger = $logger;
        $this->level = $level;
    }

    /**
     * @param string $title
     */
    public function header(string $title)
    {
        $multiplier = 60;
        $separator = str_repeat('-', $multiplier);
        $this->write($separator);
        $this->write($title);
        $this->write($separator);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function write(string $message, array $context = [])
    {
        $this->logger->log($this->level, $message, $context);
    }
}
