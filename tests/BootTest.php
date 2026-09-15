<?php

namespace Webshr\Core\Tests;

use PHPUnit\Framework\TestCase;
use Webshr\Core\Bootloader;
use Webshr\Core\Application;
use Webshr\Core\Tests\Fixtures\Modules\No_Arg_Module;
use Webshr\Core\Tests\Fixtures\Modules\App_Aware_Module;

class BootTest extends TestCase
{
    protected function setUp(): void
    {
        Stubs::reset();
        Bootloader::set_instance(null);
        No_Arg_Module::reset_counters();
        App_Aware_Module::reset_counters();
    }

    public function test_bootloader_boots_fixture_theme_without_error(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();

        $app = $bootloader->get_application();
        $this->assertTrue($app->has_been_bootstrapped());
        $this->assertTrue($app->is_booted());
    }
}
