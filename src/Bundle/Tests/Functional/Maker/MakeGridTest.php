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

namespace Sylius\Bundle\GridBundle\Tests\Functional\Maker;

use App\Entity\Book;
use App\Entity\Price;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Console\Tester\CommandTester;

final class MakeGridTest extends MakerTestCase
{
    private const BOOK_GRID_PATH = 'Grid/BookGrid.php';
    private const PRICE_GRID_PATH = 'Grid/PriceGrid.php';
    private const INVALID_GRID_PATH = 'Grid/InvalidGrid.php';

    /** @test */
    public function it_can_create_grids(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::PRICE_GRID_PATH));

        $tester->execute(['entity' => Price::class, '--namespace' => 'Tests\Tmp\Grid']);

        $this->assertFileExists(self::tempFile(self::PRICE_GRID_PATH));
        $this->assertSame(self::getPriceGridExpectedContent(), \file_get_contents(self::tempFile(self::PRICE_GRID_PATH)));
    }

    /** @test */
    public function it_can_create_grids_with_boolean_fields(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::BOOK_GRID_PATH));

        $tester->execute(['entity' => Book::class, '--namespace' => 'Tests\Tmp\Grid']);

        $this->assertFileExists(self::tempFile(self::BOOK_GRID_PATH));
        $this->assertSame(self::getBookGridExpectedContent(), \file_get_contents(self::tempFile(self::BOOK_GRID_PATH)));
    }

    /** @test */
    public function it_can_create_grids_interactively(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::PRICE_GRID_PATH));

        $tester->setInputs([Price::class]);
        $tester->execute(['--namespace' => 'Tests\Tmp\Grid']);

        $this->assertFileExists(self::tempFile(self::PRICE_GRID_PATH));
        $this->assertSame(self::getPriceGridExpectedContent(), \file_get_contents(self::tempFile('Grid/PriceGrid.php')));
    }

    /** @test */
    public function invalid_entity_throws_exception(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::INVALID_GRID_PATH));

        try {
            $tester->execute(['entity' => 'Invalid']);
        } catch (RuntimeCommandException $e) {
            $this->assertSame('Entity "Invalid" not found.', $e->getMessage());
            $this->assertFileDoesNotExist(self::tempFile(self::INVALID_GRID_PATH));

            return;
        }

        $this->fail('Exception not thrown.');
    }

    private static function getBookGridExpectedContent(): string
    {
        return <<<EOF
<?php

namespace App\Tests\Tmp\Grid;

use App\Entity\Book;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class BookGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_book';
    }

    public function buildGrid(GridBuilderInterface \$gridBuilder): void
    {
        \$gridBuilder
            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
            ->addField(
                StringField::create('title')
                    ->setLabel('Title')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('state')
                    ->setLabel('State')
                    ->setSortable(true)
            )
             //->addField(
             //    TwigField::create('enabled', 'path/to/field/template.html.twig')
             //        ->setLabel('Enabled')
             //)
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('CreatedAt')
            )
            ->addField(
                DateTimeField::create('updatedAt')
                    ->setLabel('UpdatedAt')
            )
            ->addField(
                DateTimeField::create('publishedAt')
                    ->setLabel('PublishedAt')
            )
            ->addField(
                StringField::create('price.currencyCode')
                    ->setLabel('Price.currencyCode')
                    ->setSortable(true)
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()
                )
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Book::class;
    }
}

EOF
            ;
    }

    private static function getPriceGridExpectedContent(): string
    {
        return <<<EOF
<?php

namespace App\Tests\Tmp\Grid;

use App\Entity\Price;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;

final class PriceGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_price';
    }

    public function buildGrid(GridBuilderInterface \$gridBuilder): void
    {
        \$gridBuilder
            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
            ->addField(
                StringField::create('currencyCode')
                    ->setLabel('CurrencyCode')
                    ->setSortable(true)
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()
                )
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Price::class;
    }
}

EOF
   ;
    }
}
