<?php

namespace Plugin\PluginWithComposer;

use App\Service\Plugin\AbstractPlugin;
use GuzzleHttp\Client;

require_once __DIR__ . '/vendor/autoload.php';

class PluginWithComposer extends AbstractPlugin
{
    private const NAME = 'COMPOSER_PLUGIN';

    public function getName(): string
    {
        return self::NAME;
    }

    public function doRequest(): bool
    {
        $client = new Client();
        $response = $client->get('http://localhost:8000');

        return $response->getBody()->getContents() === 'OK';
    }
}