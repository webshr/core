<?php

/**
 * @package   Webshr\Core
 * @since     1.0.0
 * @author    Webshore, H. Liebel
 * @license   https://opensource.org/licenses/GPL-3.0-or-later GPL-3.0-or-later
 * @link      https://webshore.eu/
 *
 * This file contains a customized adaptation of the Roots\Acorn package.
 * Original author: Roots
 * Original source: https://github.com/roots/acorn/blob/main/src/Roots/Acorn/
 */

namespace Webshr\Core\Filesystem;

class Filesystem
{
    /**
     * Normalizes file path separators
     *
     * @param  mixed  $path
     * @param  string  $separator
     * @return mixed
     */
    public function normalize_path($path, $separator = '/')
    {
        return preg_replace('#/+#', $separator, strtr($path, '\\', '/'));
    }

    /**
     * Find the closest file up the directory tree.
     *
     * @param  string  $path
     * @param  string  $file
     * @return string|null
     */
    public function closest($path, $file)
    {
        $currentDirectory = $path;
        while ($this->is_readable($currentDirectory)) {
            if ($this->is_file($filePath = $currentDirectory . DIRECTORY_SEPARATOR . $file)) {
                return $filePath;
            }

            $parentDirectory = $this->dirname($currentDirectory);
            if (empty($parentDirectory) || $parentDirectory === $currentDirectory) {
                break;
            }

            $currentDirectory = $parentDirectory;
        }

        return null;
    }

    /**
     * Get relative path of target from specified base
     *
     * @param  string  $base_path
     * @param  string  $target_path
     * @return string
     *
     * @copyright Fabien Potencier
     * @license   GPL-3.0-or-later
     *
     * @link      https://github.com/symfony/routing/blob/v4.1.1/Generator/UrlGenerator.php#L280-L329
     */
    public function get_relative_path($base_path, $target_path)
    {
        $base_path   = $this->normalize_path($base_path);
        $target_path = $this->normalize_path($target_path);
        if ($base_path === $target_path) {
            return '';
        }

        $source_dirs = explode('/', ltrim($base_path, '/'));
        $target_dirs = explode('/', ltrim($target_path, '/'));
        array_pop($source_dirs);
        $target_file = array_pop($target_dirs);
        foreach ($source_dirs as $i => $dir) {
            if (isset($target_dirs[$i]) && $dir === $target_dirs[$i]) {
                unset($source_dirs[$i], $target_dirs[$i]);
            } else {
                break;
            }
        }

        $target_dirs[] = $target_file;
        $path          = str_repeat('../', count($source_dirs)) . implode('/', $target_dirs);
        return $path === '' || $path[0] === '/'
            || ($colon_pos = strpos($path, ':')) !== false && ($colon_pos < ($slash_pos = strpos($path, '/'))
                || $slash_pos === false)
            ? "./$path" : $path;
    }

    /**
     * Ensure a directory exists.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return void
     */
    public function ensure_directory_exists($path, $mode = 0755, $recursive = true)
    {
        if (! $this->is_directory($path)) {
            $this->make_directory($path, $mode, $recursive);
        }
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @param  bool  $force
     * @return bool
     */
    public function make_directory($path, $mode = 0755, $recursive = false, $force = false)
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }

        return mkdir($path, $mode, $recursive);
    }

    /**
     * Extract the parent directory from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function is_directory($directory)
    {
        return is_dir($directory);
    }

    /**
     * Get all files in a directory.
     *
     * @param string $directory
     * @return array
     */
    public function files(string $directory): array
    {
        return glob($directory . '/*.php');
    }

    /**
     * Determine if the given path is a file.
     *
     * @param  string  $file
     * @return bool
     */
    public function is_file($file)
    {
        return is_file($file);
    }

    /**
     * Determine if the given path is readable.
     *
     * @param  string  $path
     * @return bool
     */
    public function is_readable($path)
    {
        return is_readable($path);
    }
}
