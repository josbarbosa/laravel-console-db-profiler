<?php namespace PackageTests\Unit;

use JosBarbosa\ConsoleDbProfiler\Traits\Printable;
use PackageTests\TestCase;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;

/**
 * Class PrintableTest
 * @package PackageTests\Unit
 */
class PrintableTest extends TestCase
{
    use Printable;

    /** @test */
    function it_returns_the_terminal_size_width(): void
    {
        putenv('COLUMNS=100');
        $this->assertEquals(100, $this->getTerminalWidthSize());
    }

    /** @test */
    function it_wraps_text_in_a_console_column_table(): void
    {
        /** Window Offset is 30 */
        putenv('COLUMNS=40');
        $str = "aaaaaaaaaaaaaaa";
        $this->assertEquals("aaaaaaaaaa\naaaaa", $this->wrapTextInColumn($str));
    }

    /** @test */
    function it_colors_text(): void
    {
        $text = 'a';
        $color = $this->colorOk;
        $this->assertEquals("<fg={$color}>{$text}</>", $this->textColor($text, $color));

        $methods = [
            'textColorInfo'      => $this->colorInfo,
            'textColorError'     => $this->colorError,
            'textColorHighlight' => $this->colorHighlight,
            'textColorOk'        => $this->colorOk,
        ];
        $text = 'a';
        foreach ($methods as $method => $color) {
            $this->assertEquals(
                "<fg={$color}>{$text}</>",
                $this->{$method}($text)
            );
        }
    }

    /** @test */
    function it_styles_text(): void
    {
        $methods = [
            'info'      => $this->tagInfo,
            'error'     => $this->tagError,
            'highlight' => $this->tagHighlight,
            'comment'   => $this->tagComment,
        ];
        $text = 'a';
        foreach ($methods as $method => $tag) {
            $this->assertEquals(
                "<{$tag}>{$text}</{$tag}>",
                $this->{$method}($text)
            );
        }
    }

    /** @test */
    function it_returns_a_table_cell(): void
    {
        $this->assertInstanceOf(TableCell::class, $this->tableCell('a', 20, 20));
        $this->assertInstanceOf(TableCell::class, $this->tableCell('a', 20));
        $this->assertInstanceOf(TableCell::class, $this->tableCell('a'));
    }

    /** @test */
    function it_returns_a_table_separator(): void
    {
        /** returns an empty array */
        $this->assertEmpty($this->tableSeparator(false));

        /** returns an instance of TableSeparator */
        $this->assertInstanceOf(TableSeparator::class, $this->tableSeparator(true));
    }
}
