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

namespace App\Migration;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Migration\ActionMethodGenerator;
use Sylius\Bundle\GridBundle\Migration\CodeGenerator;
use Sylius\Bundle\GridBundle\Migration\CodeOutputter;
use Sylius\Bundle\GridBundle\Migration\ConfigMigration;
use Sylius\Bundle\GridBundle\Migration\GridBodyGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class MigrationTest extends TestCase
{
    private ConfigMigration $configMigrator;

    public function setup(): void
    {
        chdir(__DIR__);
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->method('getParameter')->with('sylius.model.order.class')
            ->willReturn('Sylius\\Model\\Order')
        ;

        $codeGenerator = new CodeGenerator(new CodeOutputter());

        $this->configMigrator = new ConfigMigration(
            $codeGenerator,
            new GridBodyGenerator(new ActionMethodGenerator($codeGenerator), $codeGenerator),
            $container,
        );
    }

    public function testConfigurationForOrder(): void
    {
        $fileContent = file_get_contents('order.yml');
        $yaml = Yaml::parse($fileContent)['sylius_grid']['grids']['sylius_admin_order'];

        $this->configMigrator->convertGrids($fileContent);

        include 'SyliusAdminOrder.php';
        $orderGrid = new \SyliusAdminOrder();
        $generatedConfig = $orderGrid->toArray();

        // Removing expected path to be unequal and adding manual check
        unset($generatedConfig['driver']['options']['class'], $yaml['driver']['options']['class']);

        $this->assertEquals('Sylius\\Model\\Order', $orderGrid->getResourceClass());

        // If a field is not sortable in yaml you have to have it set to false in php it doesn't render so
        $this->assertArrayNotHasKey('sortable', $generatedConfig['fields']['state']);
        unset($yaml['fields']['state']['sortable']);

        // Checking that the rest of the config is equals
        $this->assertEquals($yaml, $generatedConfig);
    }

    public function testConfigurationForAdvancedConfig(): void
    {
        $fileContent = file_get_contents('advanced_configuration.yml');
        $yaml = Yaml::parse($fileContent)['sylius_grid']['grids']['foo'];

        $this->configMigrator->convertGrids($fileContent);

        include 'Foo.php';
        $orderGrid = new \Foo();

        $this->assertEquals($yaml, $orderGrid->toArray());
        $this->assertEquals(Book::class, $orderGrid->getResourceClass());
    }

    public function testConfigurationWithNamespace(): void
    {
        $fileContent = file_get_contents('namespace_grid.yaml');
        $yaml = Yaml::parse($fileContent)['sylius_grid']['grids']['foo_with_namespace'];

        $this->configMigrator->namespace = 'SomeNameSpace';
        $this->configMigrator->convertGrids($fileContent);

        include 'FooWithNamespace.php';
        $orderGrid = new \SomeNameSpace\FooWithNamespace();
        $generatedConfig = $orderGrid->toArray();

        // Removing expected path
        unset($generatedConfig['driver']['options']['class'], $yaml['driver']['options']['class']);

        $this->assertEquals('Sylius\\Model\\Order', $orderGrid->getResourceClass());

        // Checking that the rest of the config is equals
        $this->assertEquals($yaml, $generatedConfig);
    }
}
