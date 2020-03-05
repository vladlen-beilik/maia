<?php

namespace SpaceCode\Maia;

use Eusonlito\LaravelPacker\Packer;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class Maia {

    protected $version;
    protected $filesystem;
    protected $viewLoadingEvents = [];

    public function __construct() {
        $this->filesystem = app(Filesystem::class);
        $this->findVersion();
    }

    protected function findVersion() {
        if (!is_null($this->version)) {
            return;
        }
        if ($this->filesystem->exists(base_path('composer.lock'))) {
            $file = json_decode($this->filesystem->get(base_path('composer.lock')));
            foreach ($file->packages as $package) {
                if ($package->name == 'spacecode-dev/maia') {
                    $this->version = $package->version;
                    break;
                }
            }
        }
    }

    public function view($name, array $parameters = []) {
        foreach (array_get($this->viewLoadingEvents, $name, []) as $event) {
            $event($name, $parameters);
        }
        return view($name, $parameters);
    }

    public function image($file) {
        return Storage::disk(config('maia.filemanager.disk', 'public'))->url($file);
    }

    public function routes() {
        require __DIR__ . '/../routes/index.php';
    }

    public function getVersion() {
        return $this->version;
    }
}