<?php

namespace App\Service\Plugin;

class NullServiceLocator implements ServiceLocator
{

    public function get(string $name)
    {
        return null;
    }
}