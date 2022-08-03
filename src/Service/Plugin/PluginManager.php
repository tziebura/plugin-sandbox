<?php

namespace App\Service\Plugin;

use Throwable;

interface PluginManager
{
    public function install(string $name, string $depotId, string $source): bool;
    public function uninstall(string $name, string $depotId): bool;
    public function update(string $name, string $version, bool $clearCache = false): bool;
}