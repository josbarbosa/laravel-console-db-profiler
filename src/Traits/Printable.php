<?php namespace JosBarbosa\ConsoleDbProfiler\Traits;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Terminal;

/**
 * Trait Printable
 * @package JosBarbosa\ConsoleDbProfiler\Traits
 */
trait Printable
{
    /**
     * @var int
     */
    protected $offset = 30;
    /**
     * @var string
     */
    protected $colorInfo = 'green';
    /**
     * @var string
     */
    protected $colorError = 'red';
    /**
     * @var string
     */
    protected $colorHighlight = 'yellow';
    /**
     * @var string
     */
    protected $colorOk = 'blue';
    /**
     * @var string
     */
    protected $tagInfo = 'info';
    /**
     * @var string
     */
    protected $tagError = 'error';
    /**
     * @var string
     */
    protected $tagHighlight = 'question';
    /**
     * @var string
     */
    protected $tagComment = 'comment';

    /**
     * @return int
     */
    public function getTerminalWidthSize(): int
    {
        return (new Terminal())->getWidth();
    }

    /**
     * @param string $text
     * @return string
     */
    public function wrapTextInColumn(string $text): string
    {
        return wordwrap($text, $this->getTerminalWidthSize() - $this->offset, "\n", true);
    }

    /**
     * @param string $text
     * @return string
     */
    public function textColorInfo(string $text): string
    {
        return "<fg={$this->colorInfo}>{$text}</>";
    }

    /**
     * @param string $text
     * @return string
     */
    public function textColorError(string $text): string
    {
        return "<fg={$this->colorError}>{$text}</>";
    }

    /**
     * @param string $text
     * @return string
     */
    public function textColorHighlight(string $text): string
    {
        return "<fg={$this->colorHighlight}>{$text}</>";
    }

    /**
     * @param string $text
     * @return string
     */
    public function textColorOk(string $text): string
    {
        return "<fg={$this->colorOk}>{$text}</>";
    }

    /**
     * @param string $text
     * @param string $color
     * @return string
     */
    public function textColor(string $text, string $color): string
    {
        return "<fg={$color}>{$text}</>";
    }

    /**
     * @param string $text
     * @return string
     */
    public function info(string $text): string
    {
        return "<{$this->tagInfo}>{$text}</{$this->tagInfo}>";
    }

    /**
     * @param string $text
     * @return string
     */
    public function error(string $text): string
    {
        return "<{$this->tagError}>{$text}</{$this->tagError}>";
    }

    /**
     * @param string $text
     * @return string
     */
    public function highlight(string $text): string
    {
        return "<{$this->tagHighlight}>{$text}</{$this->tagHighlight}>";
    }

    /**
     * @param string $text
     * @return string
     */
    public function comment(string $text): string
    {
        return "<{$this->tagComment}>{$text}</{$this->tagComment}>";
    }

    /**
     * @param string $str
     */
    public function line(string $str = ''): void
    {
        app()->make(ConsoleOutput::class)->writeln($str);
    }

    /**
     * @param bool $drawSeparator
     * @return array|TableSeparator
     */
    public function tableSeparator(bool $drawSeparator)
    {
        return ($drawSeparator) ? new TableSeparator() : [];
    }

    /**
     * @param string $text
     * @param int $colspan
     * @param int $rowspan
     * @return TableCell
     */
    public function tableCell(string $text, int $colspan = 1, int $rowspan = 1): TableCell
    {
        return new TableCell($text, ['colspan' => $colspan, 'rowspan' => $rowspan]);
    }

    /**
     * @param array $headers
     * @param array $rows
     * @internal param ConsoleOutput $output
     */
    public function buildTable(array $headers, array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        $output = app()->make(ConsoleOutput::class);
        (new Table($output))->setHeaders($headers)->setRows($rows)->render();
    }
}
