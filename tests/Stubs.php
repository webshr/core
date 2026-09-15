<?php

namespace Webshr\Core\Tests;

class Stubs
{
    /** @var array<string, list<array>> */
    public static array $calls = [];

    public static function reset(): void
    {
        self::$calls = [];
    }

    public static function record(string $fn, array $args): void
    {
        self::$calls[$fn][] = $args;
    }
}
