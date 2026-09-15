<?php

namespace Webshr\Core\Tests;

use PHPUnit\Framework\TestCase;
use Webshr\Core\Support\Str;

class StrTest extends TestCase
{
    /**
     * L7: Str::trim() and Str::ltrim() strip Unicode whitespace
     * characters (BOM, zero-width space, LRM) from a string.
     */
    public function test_trim_and_ltrim_strip_unicode_whitespace(): void
    {
        $bom = "\xEF\xBB\xBF";
        $zwsp = "\xE2\x80\x8B";
        $lrm = "\xE2\x80\x8E";

        $input = $bom . $zwsp . $lrm . 'hello' . $zwsp . $bom;

        $this->assertSame('hello', Str::trim($input));
        $this->assertSame(
            'hello' . $zwsp . $bom,
            Str::ltrim($input)
        );
    }
}
