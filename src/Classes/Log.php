<?php namespace JosBarbosa\ConsoleDbProfiler\Classes;

use Illuminate\Support\Facades\File;

/**
 * Class Log
 * @package JosBarbosa\ConsoleDbProfiler\Classes
 */
class Log
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Log constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param string $title
     */
    public function header(string $title)
    {
        $multiplier = 60;
        $separator = str_repeat('-', $multiplier);
        $this->save($separator);
        $this->save($title);
        $this->save($separator);
    }

    public function delete()
    {
        if ($this->exists()) {
            File::delete($this->path);
        }
    }

    /**
     * @param string $message
     */
    public function save(string $message)
    {
        File::append($this->path, $message . PHP_EOL);
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        if (File::exists($this->path)) {
            return true;
        }

        return false;
    }
}
