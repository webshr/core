<?php // phpcs:disable PSR1.Files.SideEffects

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/Stubs.php';

/**
 * WordPress function stubs for testing.
 *
 * Each side-effect stub pushes its arguments into Stubs::$calls[$fn][]
 * so tests can assert on what was called and with what arguments.
 */

if (!function_exists('get_template_directory')) {
    function get_template_directory(): string
    {
        return __DIR__ . '/fixtures/theme';
    }
}

if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri(): string
    {
        return 'https://example.com/wp-content/themes/fixture';
    }
}

if (!function_exists('get_theme_file_path')) {
    function get_theme_file_path(string $file = ''): string
    {
        return get_template_directory() . ($file ? '/' . ltrim($file, '/') : '');
    }
}

if (!function_exists('__')) {
    function __(string $text, string $domain = 'default'): string
    {
        return $text;
    }
}

if (!function_exists('esc_html')) {
    function esc_html(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post(string $data): string
    {
        return strip_tags($data, '<a><abbr><b><blockquote><br><cite><code><del>'
            . '<dd><div><dl><dt><em><h1><h2><h3><h4><h5><h6><hr><i><img><li>'
            . '<ol><p><pre><q><s><small><span><strong><sub><sup><table><tbody>'
            . '<td><tfoot><th><thead><tr><u><ul>');
    }
}

if (!function_exists('wp_die')) {
    function wp_die(string $message = '', string $title = '', $args = []): void
    {
        throw new \RuntimeException($message);
    }
}

if (!function_exists('add_filter')) {
    function add_filter(
        string $hook,
        $callback,
        int $priority = 10,
        int $accepted_args = 1
    ): bool {
        \Webshr\Core\Tests\Stubs::record('add_filter', func_get_args());
        return true;
    }
}

if (!function_exists('add_action')) {
    function add_action(
        string $hook,
        $callback,
        int $priority = 10,
        int $accepted_args = 1
    ): bool {
        \Webshr\Core\Tests\Stubs::record('add_action', func_get_args());
        return true;
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters(string $hook, $value, ...$args)
    {
        \Webshr\Core\Tests\Stubs::record('apply_filters', func_get_args());
        return $value;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script(
        string $handle,
        string $src = '',
        array $deps = [],
        $ver = false,
        $args = false
    ): void {
        \Webshr\Core\Tests\Stubs::record('wp_enqueue_script', func_get_args());
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style(
        string $handle,
        string $src = '',
        array $deps = [],
        $ver = false,
        string $media = 'all'
    ): void {
        \Webshr\Core\Tests\Stubs::record('wp_enqueue_style', func_get_args());
    }
}

if (!function_exists('wp_dequeue_script')) {
    function wp_dequeue_script(string $handle): void
    {
        \Webshr\Core\Tests\Stubs::record('wp_dequeue_script', func_get_args());
    }
}

if (!function_exists('wp_dequeue_style')) {
    function wp_dequeue_style(string $handle): void
    {
        \Webshr\Core\Tests\Stubs::record('wp_dequeue_style', func_get_args());
    }
}

if (!function_exists('wp_add_inline_script')) {
    function wp_add_inline_script(
        string $handle,
        string $data,
        string $position = 'after'
    ): bool {
        \Webshr\Core\Tests\Stubs::record('wp_add_inline_script', func_get_args());
        return true;
    }
}

if (!function_exists('wp_localize_script')) {
    function wp_localize_script(
        string $handle,
        string $object_name,
        array $l10n
    ): bool {
        \Webshr\Core\Tests\Stubs::record('wp_localize_script', func_get_args());
        return true;
    }
}

if (!function_exists('wp_set_script_translations')) {
    function wp_set_script_translations(
        string $handle,
        string $domain = 'default',
        ?string $path = null
    ): bool {
        \Webshr\Core\Tests\Stubs::record(
            'wp_set_script_translations',
            func_get_args()
        );
        return true;
    }
}

if (!function_exists('wp_get_theme')) {
    function wp_get_theme(): object
    {
        return new class {
            public function get(string $key): string
            {
                $data = [
                    'TextDomain' => 'fixture-theme',
                    'Name' => 'Fixture Theme',
                ];
                return $data[$key] ?? '';
            }
        };
    }
}

if (!function_exists('add_editor_style')) {
    function add_editor_style($stylesheet = 'editor-style.css'): void
    {
        \Webshr\Core\Tests\Stubs::record('add_editor_style', func_get_args());
    }
}

if (!function_exists('_doing_it_wrong')) {
    function _doing_it_wrong(
        string $function_name,
        string $message,
        string $version
    ): void {
        \Webshr\Core\Tests\Stubs::record('_doing_it_wrong', func_get_args());
    }
}

if (!function_exists('locate_template')) {
    function locate_template(
        $template_names,
        bool $load = false,
        bool $load_once = true
    ): string {
        return '';
    }
}
