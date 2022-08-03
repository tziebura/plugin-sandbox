<?php

namespace App\Service\Plugin;

abstract class AbstractPlugin
{
    private PluginRepository $pluginRepository;
    protected ServiceLocator $serviceLocator;

    public function __construct(PluginRepository $pluginRepository, ServiceLocator $serviceLocator)
    {
        $this->pluginRepository = $pluginRepository;
        $this->serviceLocator = $serviceLocator;
    }

    abstract public function getName(): string;

    protected function isEnabled(string $depotId): bool
    {
        return $this->pluginRepository->isEnabled($this->getName(), $depotId);
    }
}