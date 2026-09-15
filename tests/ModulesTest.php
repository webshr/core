<?php

namespace Webshr\Core\Tests;

use PHPUnit\Framework\TestCase;
use Webshr\Core\Bootloader;
use Webshr\Core\Application;
use Webshr\Core\Tests\Fixtures\Modules\No_Arg_Module;
use Webshr\Core\Tests\Fixtures\Modules\App_Aware_Module;

class ModulesTest extends TestCase
{
    protected function setUp(): void
    {
        Stubs::reset();
        Bootloader::set_instance(null);
        No_Arg_Module::reset_counters();
        App_Aware_Module::reset_counters();
    }

    /**
     * C1: A module with a no-argument constructor must be registered
     * through the bootloader without an ArgumentCountError.
     */
    public function test_module_with_no_arg_constructor_is_registered(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();
        $app = $bootloader->get_application();

        $modules = $app->module_manager->modules();
        $this->assertArrayHasKey('no-arg', $modules);
        $this->assertInstanceOf(No_Arg_Module::class, $modules['no-arg']);
    }

    /**
     * C1: A module that does not override the constructor receives
     * the Application instance via $this->app.
     */
    public function test_module_without_constructor_override_receives_app(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();
        $app = $bootloader->get_application();

        $modules = $app->module_manager->modules();
        $module = $modules['app-aware'];
        $this->assertInstanceOf(App_Aware_Module::class, $module);
        $this->assertSame($app, $module->get_app());
    }

    /**
     * H2: get_module() returns the registered module instance
     * when passed the class name.
     */
    public function test_get_module_returns_registered_instance(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();
        $app = $bootloader->get_application();

        $module = $app->get_module(No_Arg_Module::class);
        $this->assertInstanceOf(No_Arg_Module::class, $module);
    }

    /**
     * M2: register() runs before boot() on each module,
     * and boot() runs exactly once.
     */
    public function test_register_runs_before_boot_and_boot_runs_once(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();

        $this->assertSame(1, No_Arg_Module::$register_count);
        $this->assertSame(1, No_Arg_Module::$boot_count);
        $this->assertSame(1, App_Aware_Module::$register_count);
        $this->assertSame(1, App_Aware_Module::$boot_count);
    }

    /**
     * M3: Requesting an unconfigured module by name throws
     * an exception instead of returning null.
     */
    public function test_unknown_module_name_throws_not_found_exception(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();
        $app = $bootloader->get_application();

        $this->expectException(\InvalidArgumentException::class);
        $app->module_manager->module('nonexistent');
    }
}
