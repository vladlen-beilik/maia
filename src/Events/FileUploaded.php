<?php

namespace SpaceCode\Maia\Events;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Queue\SerializesModels;

class FileUploaded
{
    use SerializesModels;

    /**
     * @var mixed
     */
    public $storage;

    /**
     * @var mixed
     */
    public $filePath;

    /**
     * FileUploaded constructor.
     * @param FilesystemAdapter $storage
     * @param string $filePath
     */
    public function __construct(FilesystemAdapter $storage, string $filePath)
    {
        $this->storage = $storage;
        $this->filePath = $filePath;
    }
}
