<?php

namespace App\Service\Plugin;

class JsonFilePluginRepository implements PluginRepository
{
    private const FILE_PATH = __DIR__ . '/../../../var/data/enabled_plugins.json';

    public function isEnabled(string $name, string $depotId): bool
    {
        $index = $name . '_' . $depotId;
        $plugins = json_decode(file_get_contents(self::FILE_PATH), true);
        return isset($plugins[$index]);
    }

    public function save(string $index): void
    {
        $plugins = json_decode(file_get_contents(self::FILE_PATH), true);
        $plugins[$index] = [];
        file_put_contents(self::FILE_PATH, json_encode($plugins));
    }

    public function remove(string $index): void
    {
        $plugins = json_decode(file_get_contents(self::FILE_PATH), true);
        if (isset($plugins[$index])) {
            unset($plugins[$index]);
            file_put_contents(self::FILE_PATH, json_encode($plugins));
        }
    }

    public function count(array $criteria): int
    {
        return 0;
    }
}