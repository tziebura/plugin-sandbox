<?php

namespace App\Command;

use App\Service\Plugin\PluginManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PluginManagerRemoveCommand extends Command
{
    protected static $defaultName = 'app:plugin-manager:remove';
    protected static $defaultDescription = 'Add a short description for your command';

    private PluginManager $pluginManager;

    public function __construct(PluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->pluginManager->uninstall('PluginWithComposer', '1');

        if (!$result) {
            $io->error('Failed to remove plugin.');
            return Command::FAILURE;
        }

        $io->success('Plugin removed.');

        return Command::SUCCESS;
    }
}
