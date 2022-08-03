<?php

namespace App\Service\Plugin;

interface PluginRepository
{
    public function isEnabled(string $name, string $depotId): bool;
    public function save(string $index): void;
    public function remove(string $index): void;
    public function count(array $criteria): int;
}