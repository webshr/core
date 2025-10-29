<?php

namespace Webshr\Core\Filesystem;

if (! function_exists('Webshr\Core\Filesystem\join_paths')) {
    /**
     * Join the given paths together.
     *
     * @param  string|null  $base_path
     * @param  string  ...$paths
     * @return string
     */
    function join_paths($base_path, ...$paths)
    {
        foreach ($paths as $index => $path) {
            if (empty($path)) {
                unset($paths[$index]);
            } else {
                $paths[$index] = DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
            }
        }

        return $base_path . implode('', $paths);
    }
}
