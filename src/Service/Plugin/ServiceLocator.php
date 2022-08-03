<?php

namespace App\Service\Plugin;

interface ServiceLocator
{
    /**
     * Returns requested service
     * @return mixed
     */
    public function get(string $name);
}