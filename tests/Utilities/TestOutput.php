<?php namespace PackageTests\Utilities;

use Symfony\Component\Console\Output\Output;

/**
 * Class TestOutput
 * @package PackageTests\Utilities
 */
class TestOutput extends Output
{
    /**
     * @var string
     */
    public $output = '';

    /**
     * @param string $message
     * @param bool $newline
     */
    protected function doWrite($message, $newline)
    {
        $this->output .= $message . ($newline ? "\n" : '');
    }
}
