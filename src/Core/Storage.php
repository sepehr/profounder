<?php

namespace Profounder\Core;

use Illuminate\Filesystem\Filesystem;

class Storage implements StorageContract
{
    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Storage constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function exists($path)
    {
        return $this->filesystem->exists($this->path($path));
    }

    /**
     * @inheritdoc
     */
    public function put($path, $contents, $lock = false)
    {
        return $this->filesystem->put($this->path($path), $contents, $lock);
    }

    /**
     * @inheritdoc
     */
    public function get($path, $lock = false)
    {
        return $this->filesystem->get($this->path($path), $lock);
    }

    /**
     * @inheritdoc
     */
    public function delete($paths)
    {
        return $this->filesystem->delete($this->path($paths));
    }

    /**
     * @inheritdoc
     */
    public function move($path, $target)
    {
        return $this->filesystem->move($this->path($path), $this->path($target));
    }

    /**
     * Returns storage path.
     *
     * @param string|array $path
     *
     * @return string|array
     */
    private function path($path)
    {
        if (is_array($path)) {
            return array_map('storage_path', $path);
        }

        return storage_path($path);
    }
}
