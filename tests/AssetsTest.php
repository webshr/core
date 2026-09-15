<?php

namespace Webshr\Core\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Webshr\Core\Bootloader;
use Webshr\Core\Application;
use Webshr\Core\Assets\Asset_Factory;
use Webshr\Core\Assets\Asset\Php_Asset;
use Webshr\Core\Assets\Bundle;
use Webshr\Core\Tests\Fixtures\Modules\No_Arg_Module;
use Webshr\Core\Tests\Fixtures\Modules\App_Aware_Module;

class AssetsTest extends TestCase
{
    protected function setUp(): void
    {
        Stubs::reset();
        Bootloader::set_instance(null);
        No_Arg_Module::reset_counters();
        App_Aware_Module::reset_counters();
        Bundle::reset_inlined_sources();
    }

    /**
     * Consumer surface: meta('theme.js') resolves version and dependencies
     * from the .asset.php file.
     */
    public function test_meta_asset_resolves_version_and_dependencies(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();

        $meta = \Webshr\Core\meta('theme.js');
        $this->assertSame('abc123', $meta->version());
        $this->assertNotEmpty($meta->dependencies());
    }

    /**
     * C3: Asset_Factory::create() with type 'php' returns an instance of
     * the correctly cased Php_Asset class, not the non-existent PHP_Asset.
     */
    public function test_php_asset_factory_uses_correctly_cased_class(): void
    {
        $reflection = new ReflectionClass(Php_Asset::class);
        $this->assertSame('Php_Asset', $reflection->getShortName());

        $asset = Asset_Factory::create(
            __DIR__ . '/fixtures/theme/build/theme.asset.php',
            'https://example.com/build/theme.asset.php',
            'php'
        );
        $this->assertInstanceOf(Php_Asset::class, $asset);
    }

    /**
     * H1: Bundle::inline() calls wp_add_inline_script with a handle
     * of the form {bundle-id}/{script-handle} on the last JS handle.
     */
    public function test_bundle_inline_adds_inline_script_to_last_js_handle(): void
    {
        $bundle = $this->createThemeBundle();
        $bundle->inline('console.log("test")', 'after');

        $calls = Stubs::$calls['wp_add_inline_script'] ?? [];
        $this->assertNotEmpty($calls, 'wp_add_inline_script was not called');
        $this->assertStringContainsString('theme/', $calls[0][0]);
    }

    /**
     * H1: Bundle::localize() calls wp_localize_script with a handle
     * targeting the first JS handle in the bundle.
     */
    public function test_bundle_localize_targets_first_js_handle(): void
    {
        $bundle = $this->createThemeBundle();
        $bundle->localize('myData', ['key' => 'value']);

        $calls = Stubs::$calls['wp_localize_script'] ?? [];
        $this->assertNotEmpty($calls, 'wp_localize_script was not called');
        $this->assertStringContainsString('theme/', $calls[0][0]);
    }

    /**
     * H1: Bundle::translate() calls wp_set_script_translations for
     * every JS handle in the bundle.
     */
    public function test_bundle_translate_registers_translations_for_every_js_handle(): void
    {
        $bundle = $this->createThemeBundle();
        $bundle->translate('my-domain', '/path/to/languages');

        $calls = Stubs::$calls['wp_set_script_translations'] ?? [];
        $this->assertNotEmpty(
            $calls,
            'wp_set_script_translations was not called'
        );
        $this->assertStringContainsString('theme/', $calls[0][0]);
    }

    private function createThemeBundle(): Bundle
    {
        return new Bundle(
            'theme',
            [
                'js' => ['app' => 'theme.js'],
                'css' => ['app' => 'theme.css'],
                'dependencies' => ['wp-i18n'],
            ],
            __DIR__ . '/fixtures/theme/build',
            'https://example.com/build'
        );
    }
}
