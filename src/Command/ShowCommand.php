<?php

namespace ToolInstaller\Composer\Command;

use Composer\Command\BaseCommand;
use Composer\Factory;
use Composer\Json\JsonFile;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use ToolInstaller\Composer\Defaults;

class ShowCommand extends BaseCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tool-installer:show')
            ->setDescription('@todo');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $file = Factory::getComposerFile();
        $json = new JsonFile($file);

        $composerDefinition = $json->read();
        $rows = [];

        $io->title('Current configuration:');

        foreach ($composerDefinition['extra']['tools'] as $name => $parameters) {
            $onlyDev = (true === Defaults::DEV_MODE) ? 'yes' : 'no';
            $forceReplace = (true === Defaults::FORCE_REPLACE) ? 'yes' : 'no';
            $signUrl = '-';

            if (isset($parameters['only-dev'])) {
                $onlyDev = (true === $parameters['only-dev']) ? 'yes' : 'no';
            }

            if (isset($parameters['force-replace'])) {
                $forceReplace = (true === $parameters['force-replace']) ? 'yes' : 'no';
            }

            if (isset($parameters['sign-url'])) {
                $signUrl = $parameters['sign-url'];
            }

            $rows[] = [$name, $parameters['url'], $signUrl, $onlyDev, $forceReplace];
        }

        $io->table(['Tool', 'Url', 'Sign url', 'Only dev', 'Force replace'], $rows);
    }
}
