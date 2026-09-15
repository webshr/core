<?php

namespace Webshr\Core\Tests\Fixtures\Modules;

use Webshr\Core\Module;

class App_Aware_Module extends Module
{
    public static int $register_count = 0;
    public static int $boot_count = 0;

    public function can_register(): bool
    {
        return true;
    }

    public function register(): void
    {
        self::$register_count++;
    }

    public function boot(): void
    {
        self::$boot_count++;
    }

    public function get_app()
    {
        return $this->app;
    }

    public static function reset_counters(): void
    {
        self::$register_count = 0;
        self::$boot_count = 0;
    }
}
