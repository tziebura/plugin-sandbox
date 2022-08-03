<?php

namespace App\Service\Plugin;

use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class BitbucketPluginsManager implements PluginManager
{
    private const PLUGINS_DIR_PATH = __DIR__ . '/../../../plugins';
    private const CONSOLE = __DIR__ . '/../../../bin/console';

    private PluginRepository $pluginRepository;

    public function __construct(PluginRepository $pluginRepository)
    {
        $this->pluginRepository = $pluginRepository;
    }

    public function install(string $name, string $depotId, string $source): bool
    {
        $checklist = [
            'clone' => false,
            'composer_install' => false,
            'save_in_repository' => false,
            'cache_clear' => false,
        ];

        $pluginPath = self::PLUGINS_DIR_PATH . '/' . $name;

        try {
            if (!file_exists($pluginPath)) {
                $clonePluginProcess = new Process(['git', 'clone', $source, $pluginPath]);
                $clonePluginProcess->run();

                if (!$clonePluginProcess->isSuccessful()) {
                    throw new RuntimeException('Failed to clone the plugin.');
                }

                $checklist['clone'] = true;
                $composerInstallProcess = new Process(['composer', 'install', '--working-dir=' . $pluginPath]);
                $composerInstallProcess->run();

                if (!$composerInstallProcess->isSuccessful()) {
                    throw new RuntimeException('Failed to install plugins dependencies.');
                }

                $checklist['composer_install'] = true;
            }

            $this->pluginRepository->save($name . '_' . $depotId);
            $checklist['save_in_repository'] = true;

            $cacheClearProcess = new Process([self::CONSOLE, 'cache:clear' , '--env=prod']);
            $cacheClearProcess->run();

            if (!$cacheClearProcess->isSuccessful()) {
                throw new RuntimeException('Failed to clear cache.');
            }

            echo $cacheClearProcess->getOutput() . PHP_EOL;
            $checklist['cache_clear'] = true;
            return true;
        } catch (Throwable $throwable) {
            echo $throwable->getMessage() . PHP_EOL;

            if ($checklist['clone'] || $checklist['composer_install']) {
                $removeDirectoryProcess = new Process(['rm', '-rf', $pluginPath]);
                $removeDirectoryProcess->run();
            }

            if ($checklist['save_in_repository']) {
                $this->pluginRepository->remove($name . '_' . $depotId);
            }

            return false;
        }
    }

    public function uninstall(string $name, string $depotId): bool
    {
        $this->pluginRepository->remove($name . '_' . $depotId);

        if ($this->pluginRepository->count(['name' => $name]) > 0) {
            return true;
        }

        // Cleanup if the plugin is not used
        $removeDirectoryProcess = new Process(['rm', '-rf', self::PLUGINS_DIR_PATH . '/' . $name]);
        $removeDirectoryProcess->run();

        // Clear cache - might not be necessary
        $cacheClearProcess = new Process([self::CONSOLE, 'cache:clear' , '--env=prod']);
        $cacheClearProcess->run();

        return true;
    }

    public function update(string $name, string $version, bool $clearCache = false): bool
    {
        $pluginPath = self::PLUGINS_DIR_PATH . '/' . $name;
        $gitFetchProcess = new Process(['git', '--git-dir=' . $pluginPath . '/.git', 'fetch']);
        $gitFetchProcess->run();

        $gitTagProcess = new Process(['git', '--git-dir=' . $pluginPath . '/.git', 'tag']);
        $gitTagProcess->run();
        $tags = array_flip(array_filter(explode("\n", $gitTagProcess->getOutput()), 'strlen'));

        if (!isset($tags[$version])) {
            throw new RuntimeException(sprintf('Invalid version %s, valid versions are: %s',
                $version,
                implode(', ', array_keys($tags))
            ));
        }

        $currentTagProcess = new Process(['git', '--git-dir=' . $pluginPath . '/.git', 'describe', '--tags']);
        $currentTagProcess->run();

        $currentTag = str_replace(PHP_EOL, '', $currentTagProcess->getOutput());

        if ($version === $currentTag) {
            return true;
        }

        $checkoutProcess = new Process(['git', '--git-dir=' . $pluginPath . '/.git', '--work-tree=' . $pluginPath, 'checkout', '--force', $version]);
        $checkoutProcess->run();

        if (!$checkoutProcess->isSuccessful()) {
            throw new RuntimeException($checkoutProcess->getErrorOutput());
        }

        if ($clearCache) {
            $cacheClearProcess = new Process([self::CONSOLE, 'cache:clear' , '--env=prod']);
            $cacheClearProcess->run();
        }

        return true;
    }
}