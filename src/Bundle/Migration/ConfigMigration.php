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

namespace Sylius\Bundle\GridBundle\Migration;

use InvalidArgumentException;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\SubItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Config\GridConfig;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigMigration
{
    public ?string $namespace = null;

    public function __construct(
        private CodeGenerator $codeGenerator,
        private GridBodyGenerator $gridBodyGenerator,
        private ContainerInterface $container,
    ) {
    }

    public function convertGrids(string $fileContent): void
    {
        $data = Yaml::parse($fileContent);

        if (!is_array($data)) {
            throw new InvalidArgumentException('Parsing of the file was either not successful or the file does not contain a valid configuration');
        }

        $allGrids = $data['sylius_grid']['grids'] ?? [];
        if (count($allGrids) === 0) {
            throw new InvalidArgumentException('Could not find any grids in the parsed file');
        }

        foreach ($allGrids as $gridName => $gridConfiguration) {
            [$fileName, $code] = $this->convertGrid($gridName, $gridConfiguration);
            file_put_contents($fileName, $code);
        }
    }

    public function convertGrid(string $gridName, array $gridConfiguration): array
    {
        if ($this->namespace !== null) {
            $this->codeGenerator->setNamespace($this->namespace);
        }

        foreach ([
            AbstractGrid::class,
            ResourceAwareGridInterface::class,
            Filter::class,
            Field::class,
            GridBuilderInterface::class,
            MainActionGroup::class,
            ItemActionGroup::class,
            SubItemActionGroup::class,
            BulkActionGroup::class,
            GridConfig::class,
            GridBuilder::class,
            Action::class,
            ShowAction::class,
            CreateAction::class,
            UpdateAction::class,
            DeleteAction::class,
            DateTimeField::class,
            StringField::class,
            TwigField::class,
        ] as $class) {
            $this->codeGenerator->addUseStatement($class);
        }

        $resourceClass = $this->getResourceClass($gridConfiguration);
        $className = $this->convertName($gridName);

        $statement = $this->gridBodyGenerator->getGridBuilderBody(new Variable('gridBuilder'), $gridConfiguration);
        if ($statement === null) {
            $stmts = [];
            trigger_error('You have an empty grid.', \E_USER_NOTICE);
        } else {
            $stmts = [new Expression($statement)];
        }

        $this->codeGenerator->addClass(
            $className,
            'AbstractGrid',
            ['ResourceAwareGridInterface'],
            [
                CodeGenerator::createStaticFunction('getName', $gridName),
                CodeGenerator::createNonStaticFunction('getResourceClass', $resourceClass),
                CodeGenerator::createFunction(
                    'buildGrid',
                    [['GridBuilderInterface', 'gridBuilder']],
                    $stmts,
                ),
            ],
        );

        return [
            $className . '.php',
            $this->codeGenerator->build(),
        ];
    }

    /**
     * Converts a name from sylius_admin_order
     * to: SyliusAdminOrder
     *
     * Which then can be used as the class name
     */
    public function convertName(string $gridName): string
    {
        return ucfirst(preg_replace_callback('#_\w#', static fn ($a) => strtoupper($a[0][1]), $gridName));
    }

    public function getResourceClass(array $gridConfiguration): string
    {
        $resourceClass = $gridConfiguration['driver']['options']['class'] ?? 'To be replaced with the correct class.';

        if (strpos($resourceClass, '%') !== false) {
            return $this->container->getParameter(substr($resourceClass, 1, strlen($resourceClass) - 2));
        }

        return $resourceClass;
    }
}
