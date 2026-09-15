# Webshr Core

A modern, modular framework for WordPress projects built with PHP 8.1+ and inspired by Laravel's architecture.

## Overview

Webshr Core is a lightweight yet powerful framework designed specifically for WordPress development. It provides a clean, object-oriented approach to building WordPress themes and plugins with modern PHP features, dependency injection, and a modular architecture.

## Features

- **Modern PHP Architecture**: Built with PHP 8.1+ using PSR-4 autoloading
- **Modular System**: Extensible module-based architecture for clean code organization
- **Asset Management**: Comprehensive asset handling with manifest support and bundling
- **Dependency Injection**: Service container for managing dependencies and bindings
- **Encryption & Security**: Built-in encryption utilities with multiple cipher support
- **Configuration Management**: Flexible configuration system with environment support
- **Helper Functions**: Rich set of utility functions for common WordPress tasks
- **String Utilities**: Comprehensive string manipulation helpers
- **Filesystem Abstraction**: Clean filesystem operations with proper error handling

## Installation

```bash
composer require webshr/core
```

## Quick Start

### Basic Setup

```php
<?php
// functions.php or your theme's bootstrap file

use Webshr\Core\Bootloader;

// Initialize the framework
$bootloader = Bootloader::get_instance();
$bootloader->boot();
```

### Using the Application Container

```php
use function Webshr\Core\app;

// Get the application instance
$app = app();

// Resolve services from the container
$encryption = app('encryption');
$assets = app('assets');
```

## Core Components

### Application Container

The `Application` class serves as the main service container and communication hub:

```php
use Webshr\Core\Application;

$app = new Application();

// Bind services
$app->instance('my-service', new MyService());

// Resolve services
$service = $app->make('my-service');

// Register singletons
$app->singleton('database', Database::class);
```

### Module System

Create modular functionality using the module system:

```php
use Webshr\Core\Module;

class MyModule extends Module
{
    public function register(): void
    {
        // Register WordPress hooks, services, etc.
        add_action('init', [$this, 'init']);
    }

    public function boot(): void
    {
        // Boot logic after registration
    }

    public function can_register(): bool
    {
        return true; // Conditional registration
    }
}
```

### Asset Management

Handle assets with manifest support and bundling:

```php
use Webshr\Core\asset;
use Webshr\Core\bundle;

// Get individual assets
$css = asset('styles/main.css');
$js = asset('scripts/app.js');

// Get asset bundles
$mainBundle = bundle('main');

// Enqueue CSS files
$mainBundle->css(function($handle, $src) {
    wp_enqueue_style($handle, $src);
});

// Enqueue JS files
$mainBundle->js(function($handle, $src, $deps) {
    wp_enqueue_script($handle, $src, $deps);
});
```

### Encryption Utilities

Secure data with built-in encryption:

```php
use Webshr\Core\encrypt;
use Webshr\Core\decrypt;

// Encrypt data
$encrypted = encrypt('sensitive data');

// Decrypt data
$decrypted = decrypt($encrypted);

// String-specific encryption (no serialization)
$encrypted = encrypt_string('plain text');
$decrypted = decrypt_string($encrypted);
```

### String Utilities

Rich string manipulation helpers:

```php
use Webshr\Core\Support\Str;

// String operations
Str::contains('Hello World', 'World'); // true
Str::starts_with('Hello World', 'Hello'); // true
Str::ends_with('Hello World', 'World'); // true

// String extraction
Str::before('user@example.com', '@'); // 'user'
Str::after('user@example.com', '@'); // 'example.com'
Str::between_first('Hello [World]', '[', ']'); // 'World'

// Pattern matching
Str::is('admin/*', 'admin/users'); // true
```

## Configuration

### Environment Configuration

The framework supports environment-based configuration:

```php
// config/app.php
return [
    'paths' => [
        'base' => get_template_directory(),
        'config' => get_template_directory() . '/config',
        'resources' => get_template_directory() . '/resources',
        'storage' => get_template_directory() . '/storage',
    ],
    'modules' => [
        'theme' => ThemeModule::class,
        'assets' => AssetModule::class,
    ],
    'cipher' => 'aes-256-cbc',
    'key' => env('APP_KEY'),
];
```

### Asset Configuration

Configure asset manifests and bundles:

```php
// config/assets.php
return [
    'default' => 'main',
    'manifests' => [
        'main' => [
            'path' => get_template_directory() . '/dist',
            'url' => get_template_directory_uri() . '/dist',
            'assets' => get_template_directory() . '/dist/manifest.json',
            'bundles' => get_template_directory() . '/dist/bundles.json',
        ],
    ],
];
```

## Helper Functions

The framework provides numerous helper functions:

```php
// Application helpers
app(); // Get application instance
bootloader(); // Get bootloader instance

// Asset helpers
asset('path/to/file.css');
bundle('main');
meta('path/to/file.json');

// Module helpers
module('theme');

// Security helpers
encrypt($data);
decrypt($payload);
hash($data);
check($data, $hash);

// WordPress helpers
add_actions(['init', 'wp_enqueue_scripts'], $callback);
add_filters(['the_content', 'the_title'], $callback);
require_files('/path/to/directory');
```

## Architecture

### Directory Structure

```
src/
├── Application.php          # Main application container
├── Bootloader.php          # Application bootstrapper
├── Module.php              # Base module class
├── Assets/                 # Asset management system
│   ├── Manager.php         # Asset manager
│   ├── Manifest.php       # Asset manifest handler
│   ├── Bundle.php         # Asset bundle handler
│   └── Asset/             # Asset types
├── Modules/                # Module system
│   ├── Manager.php        # Module manager
│   └── Contracts/         # Module interfaces
├── Support/                # Support utilities
│   ├── Str.php            # String utilities
│   └── Traits/             # Reusable traits
├── Utility/                # Utility classes
│   ├── Encryption.php     # Encryption utilities
│   └── Hash.php           # Hashing utilities
├── Config/                 # Configuration management
├── Filesystem/             # Filesystem abstraction
└── Contracts/              # Framework interfaces
```

### Key Design Patterns

- **Service Container**: Dependency injection and service resolution
- **Module Pattern**: Extensible, modular architecture
- **Manager Pattern**: Centralized management of resources
- **Factory Pattern**: Asset creation and instantiation
- **Strategy Pattern**: Pluggable encryption and hashing algorithms

## Requirements

- PHP 8.1 or higher
- WordPress 5.0 or higher
- Composer for dependency management

## Dependencies

- `psr/container`: PSR-11 container interface
- `phpunit/phpunit`: Testing framework (dev)
- `squizlabs/php_codesniffer`: Code quality (dev)
- `wp-coding-standards/wpcs`: WordPress coding standards (dev)

## Development

### Running Tests

```bash
composer test
```

### Code Quality

```bash
composer lint
composer lint:fix
```

### Coverage

```bash
composer coverage
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## License

This project is licensed under the GPL-3.0-or-later License - see the [LICENSE.md](LICENSE.md) file for details.

## Credits

- **Author**: Henrik Liebel (Webshore)
- **Inspired by**: Laravel Framework, Roots Acorn
- **Website**: [webshore.eu](https://webshore.eu/)

## Support

For support and questions, please visit the [project repository](https://github.com/webshr/core) or contact [mail@henrikliebel.com](mailto:mail@henrikliebel.com).
