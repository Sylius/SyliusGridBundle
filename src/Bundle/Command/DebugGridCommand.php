<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Command;

use Sylius\Component\Grid\Provider\GridProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Dumper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Service\ServiceProviderInterface;

#[AsCommand(name: 'sylius:debug:grid', description: 'Debug grid configuration')]
final class DebugGridCommand extends Command
{
    public function __construct(
        private readonly GridProviderInterface $gridProvider,
        private readonly ServiceProviderInterface $taggedGrids,
        private readonly array $gridConfigurations,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(name: 'grid', mode: InputArgument::OPTIONAL, description: 'The name or fully-qualified class name (FQCN) of the grid to debug')
            ->setHelp(
                <<<'EOF'
The <info>%command.name%</info> command displays all configured grids:

  <info>php %command.full_name%</info>
  
To get specific grid, specify its name (or FQCN):

  <info>php %command.full_name% sylius_product</info>
  
  <info>php %command.full_name% App\Grid\SupplierGrid</info>
EOF
            )
        ;
    }

    public function interact(InputInterface $input, OutputInterface $output): void
    {
        if ($input->getArgument('grid')) {
            return;
        }

        $io = new SymfonyStyle($input, $output);

        $entity = $io->choice('Which grid do you want to debug?', $this->getGridChoices());

        $input->setArgument('grid', $entity);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dumper = new Dumper($output);

        /** @var string $grid */
        $grid = $input->getArgument('grid');

        $gridDefinition = $this->gridProvider->get($grid);
        $io->title(sprintf('Definition of "%s" Grid', $gridDefinition->getCode()));

        $rows = [];

        foreach ($this->objectToArray($gridDefinition) as $key => $value) {
            $rows[] = [$key, $dumper($value)];
        }

        $io->table(['Option', 'Value'], $rows);

        return Command::SUCCESS;
    }

    private function objectToArray(object $object): array
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $reflection = new \ReflectionClass($object);

        $values = [];

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();

            if ($accessor->isReadable($object, $propertyName)) {
                $values[$property->getName()] = $accessor->getValue($object, $propertyName);
            }
        }

        return $values;
    }

    private function getGridChoices(): array
    {
        $grids = array_merge(
            $this->taggedGrids->getProvidedServices(),
            array_keys($this->gridConfigurations),
        );

        \sort($grids);

        return $grids;
    }
}
