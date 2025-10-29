<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @version   1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 */

namespace Webshr\Core\Filesystem;

use DirectoryIterator;

/**
 * Provides a set of methods that are used to load files.
 */
class Loader
{
    /**
     * Iterates through a directory and executes the provided callback function
     * on each file or folder in the directory (excluding dot files).
     *
     * @since 0.1.0
     *
     * @param string   $dir Absolute path to the directory.
     * @param callable $callback The callback function.
     *
     * @return array An array of the callback results.
     */
    public function iterate_dir(string $dir, callable $callback): array
    {
        $output = [];
        if (! is_dir($dir)) {
            return $output;
        }

        $directory_iterator = new DirectoryIterator($dir);
        foreach ($directory_iterator as $file) {
            if ($file->isDot()) {
                continue;
            }

            $callback_result = call_user_func($callback, $file);
            $output[]        = $callback_result;
        }

        return $output;
    }

    /**
     * Recursively require all files in a specific directory.
     *
     * By default, requires all php files in a specific directory once.
     * Optionally able to specify the files in an array to load in a certain order.
     * Starting and trailing slashes will be stripped for the directory and all files provided.
     *
     * @since 0.1.0
     *
     * @param string $dir Directory to search through.
     * @param array $files Optional array of files to include. If this is set, only the files specified will be loaded.
     */
    public function load(string $dir, array $files = []): void
    {
        $dir = trim($dir, '/');
        if ($files === []) {
            $dir       = get_template_directory() . '/' . $dir;
            $php_files = [];
            $this->iterate_dir($dir, function ($file) use (&$php_files): void {

                if ($file->isDir()) {
                    $dir_path = trim(str_replace(get_template_directory(), '', $file->getPathname()), '/');
                    $this->load($dir_path);
                } elseif ($file->isFile() && $file->getExtension() === 'php') {
                    $file_path   = $file->getPathname();
                    $php_files[] = $file_path;
                }
            });
            // Sort files alphabetically.
            sort($php_files);
            foreach ($php_files as $php_file) {
                require_once $php_file;
            }
        } else {
            sort($files);
            foreach ($files as $file) {
                $file      = $file ?? '';
                // Fix Passing null to parameter #1 ($string) of type string is deprecated error.
                $file_path = $dir . '/' . ltrim($file, '/');
                if (locate_template($file_path, true, true) === '' || locate_template($file_path, true, true) === '0') {
                    trigger_error(sprintf(__('Error locating %s for inclusion', 'webshr'), $file_path), E_USER_ERROR,);
                }
            }
        }
    }
}
