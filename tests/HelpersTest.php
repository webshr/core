<?php

namespace Webshr\Core\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionFunction;
use Webshr\Core\Bootloader;
use Webshr\Core\Tests\Fixtures\Modules\No_Arg_Module;
use Webshr\Core\Tests\Fixtures\Modules\App_Aware_Module;

class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        Stubs::reset();
        Bootloader::set_instance(null);
        No_Arg_Module::reset_counters();
        App_Aware_Module::reset_counters();
    }

    /**
     * H3: A configured alias whose name is NOT a container binding and whose
     * target is a class name resolves to the bound instance of that class.
     */
    public function test_class_target_alias_resolves_to_bound_instance(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();
        $app = $bootloader->get_application();
        $app->use_aliases(['crypt' => \Webshr\Core\Utility\Encryption::class]);

        $this->assertSame($app->get('encryption'), $app->crypt());
    }

    /**
     * H3: A configured alias whose target is an existing binding key
     * resolves to that binding.
     */
    public function test_binding_target_alias_resolves_to_binding(): void
    {
        $bootloader = Bootloader::get_instance();
        $bootloader->boot();
        $app = $bootloader->get_application();
        $app->use_aliases(['enc_alias' => 'encryption']);

        $this->assertInstanceOf(\Webshr\Core\Utility\Encryption::class, $app->enc_alias());
    }

    /**
     * L4: When paths.resources is not configured, the language path
     * falls back to <base>/lang instead of being left unset.
     */
    public function test_lang_path_falls_back_to_base_when_resources_missing(): void
    {
        $app = new \Webshr\Core\Application(
            encryption: ['cipher' => 'aes-256-cbc', 'key' => str_repeat('k', 32)],
            base_path: __DIR__ . '/fixtures/theme',
            paths: ['base' => __DIR__ . '/fixtures/theme'],
        );

        $this->assertSame(__DIR__ . '/fixtures/theme/lang', $app->get('path.lang'));
    }

    /**
     * H4: require_files() declares $dir as a required parameter
     * (non-nullable, no default).
     */
    public function test_require_files_requires_directory_argument(): void
    {
        $ref = new ReflectionFunction('Webshr\Core\require_files');
        $param = $ref->getParameters()[0];

        $this->assertFalse(
            $param->allowsNull() && $param->isDefaultValueAvailable(),
            'The $dir parameter should not be nullable with a default'
        );
    }

    /**
     * L2: wp_die() strips <script> tags from all parameters while
     * keeping safe HTML like <code> and <a>.
     */
    public function test_wp_die_strips_script_tags_from_every_argument(): void
    {
        $caught = null;
        try {
            \Webshr\Core\wp_die(
                '<script>alert("xss")</script><code>safe</code>',
                '<script>alert("xss")</script>subtitle',
                '<script>alert("xss")</script>title',
                '<script>alert("xss")</script>footer',
            );
        } catch (\RuntimeException $e) {
            $caught = $e;
        }

        $this->assertNotNull($caught, 'wp_die should throw RuntimeException');
        $message = $caught->getMessage();
        $this->assertStringNotContainsString(
            '<script>',
            $message,
            'Script tags must be stripped from the wp_die output'
        );
        $this->assertStringContainsString('<code>', $message);
    }
}
