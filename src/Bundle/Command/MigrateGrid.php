<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Command;

use InvalidArgumentException;
use Sylius\Bundle\GridBundle\Migration\ConfigMigration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MigrateGrid extends Command
{
    public function __construct(
        private ConfigMigration $configMigration,
    ) {
    }

    protected function configure(): void
    {
        $this->setName('sylius:grid:migrate');
        $this->addArgument('fileName', InputArgument::REQUIRED, 'A file containing the Yaml configuration of the grids');
        $this->addOption('outputDirectory', null, InputOption::VALUE_OPTIONAL, 'Where should the newly generated files be stored', getcwd());
        $this->addOption('namespace', null, InputOption::VALUE_REQUIRED, 'What namespace should the generated class or function have', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileContent = file_get_contents((string) $input->getArgument('fileName'));
        if (!is_string($fileContent)) {
            throw new InvalidArgumentException('Could not read file: ' . $input->getArgument('fileName'));
        }
        $namespace = $input->getOption('namespace');
        if ($namespace !== null) {
            $namespace = (string) $namespace;
        }

        $this->configMigration->namespace = $namespace;
        $this->configMigration->convertGrids($fileContent);

        return 0;
    }
}
