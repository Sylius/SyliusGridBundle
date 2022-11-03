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

use DirectoryIterator;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\GridBundle\Migration\CodeGenerator;
use Sylius\Bundle\GridBundle\Migration\CodeOutputter;
use Sylius\Bundle\GridBundle\Migration\ConfigMigration;
use Sylius\Bundle\GridBundle\Migration\GridBodyGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class MigrationTest extends TestCase
{
    public function setup(): void
    {
        chdir(__DIR__);
    }

    private function createConfigMirator(): ConfigMigration
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->atLeast(1))
            ->method('getParameter')->with('sylius.model.order.class')
            ->willReturn('Sylius\\Model\\Order')
        ;

        return new ConfigMigration(
            new CodeGenerator(new CodeOutputter()),
            new GridBodyGenerator(),
            $container,
        );
    }

    private function compileFile(ConfigMigration $builder): void
    {
        foreach (new DirectoryIterator('.') as $file) {
            /** @var DirectoryIterator $file */
            if (in_array($file->getExtension(), ['yaml', 'yml'])) {
                $fileName = $file->getFilename();
                $builder->convertGrids(file_get_contents($fileName));
            }
        }
    }

    public function testConfigurationForOrder(): void
    {
        $yaml = Yaml::parse(file_get_contents('order.yml'))['sylius_grid']['grids']['sylius_admin_order'];

        $this->compileFile($this->createConfigMirator());
        include 'SyliusAdminOrder.php';
        $orderGrid = new \SyliusAdminOrder();

        $this->assertEquals($yaml, $orderGrid->toArray());
        $this->assertEquals('Sylius\\Model\\Order', $orderGrid->getResourceClass());
    }

    public function testConfigurationForAdvancedConfig(): void
    {
        $yaml = Yaml::parse(file_get_contents('advanced_configuration.yml'))['sylius_grid']['grids']['foo'];

        $this->compileFile($this->createConfigMirator());
        include 'Foo.php';
        $orderGrid = new \Foo();

        $this->assertEquals($yaml, $orderGrid->toArray());
        $this->assertEquals('Sylius\\Model\\Order', $orderGrid->getResourceClass());
    }

    public function testConfigurationWithNamespace(): void
    {
        $yaml = Yaml::parse(file_get_contents('advanced_configuration.yml'))['sylius_grid']['grids']['foo'];

        $migrator = $this->createConfigMirator();
        $migrator->namespace = 'SomeNameSpace';

        $this->compileFile($migrator);
        include 'FooWithNamespace.php';
        $orderGrid = new \SomeNameSpace\FooWithNamespace();

        $this->assertEquals($yaml, $orderGrid->toArray());
        $this->assertEquals('Sylius\\Model\\Order', $orderGrid->getResourceClass());
    }
}
