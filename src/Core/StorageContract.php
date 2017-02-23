<?php

namespace Profounder\Core;

interface StorageContract
{
    /**
     * Checks whether a path exists or not.
     *
     * @param  string $path
     *
     * @return bool
     */
    public function exists($path);

    /**
     * Write the contents of a file.
     *
     * @param  string $path
     * @param  string $contents
     * @param  bool $lock
     *
     * @return int
     */
    public function put($path, $contents, $lock = false);

    /**
     * Get the contents of a file.
     *
     * @param  string $path
     * @param  bool $lock
     *
     * @return string
     */
    public function get($path, $lock = false);

    /**
     * Delete the file at a given path.
     *
     * @param  string|array $paths
     *
     * @return bool
     */
    public function delete($paths);

    /**
     * Move a file to a new location.
     *
     * @param  string $path
     * @param  string $target
     *
     * @return bool
     */
    public function move($path, $target);
}
