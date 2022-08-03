<?php

namespace App\Service\Plugin;

abstract class AbstractPlugin
{
    private PluginRepository $pluginRepository;

    public function __construct(PluginRepository $pluginRepository)
    {
        $this->pluginRepository = $pluginRepository;
    }

    abstract public function getName(): string;

    public function isEnabled(string $depotId): bool
    {
        return $this->pluginRepository->isEnabled($this->getName(), $depotId);
    }
}