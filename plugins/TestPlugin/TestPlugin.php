<?php

namespace Plugin\TestPlugin;

use App\Service\Plugin\AbstractPlugin;

class TestPlugin extends AbstractPlugin
{
    private const NAME = 'TEST_PLUGIN';

    public function getName(): string
    {
        return self::NAME;
    }
}