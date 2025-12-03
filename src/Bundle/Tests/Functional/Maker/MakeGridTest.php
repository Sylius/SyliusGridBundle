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

namespace Sylius\Bundle\GridBundle\Tests\Functional\Maker;

use App\BoardGameBlog\Domain\Model\BoardGame;
use App\Entity\AdminUser;
use App\Entity\Book;
use App\Entity\Price;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylius\Bundle\GridBundle\Maker\MakeGrid;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(MakeGrid::class)]
final class MakeGridTest extends MakerTestCase
{
    private const ADMIN_USER_GRID_PATH = 'Grid/AdminUserGrid.php';

    private const BOOK_GRID_PATH = 'Grid/BookGrid.php';

    private const PRICE_GRID_PATH = 'Grid/PriceGrid.php';

    private const INVALID_GRID_PATH = 'Grid/InvalidGrid.php';

    private const BOARD_GAME_GRID_PATH = 'Grid/BoardGameGrid.php';

    /** @test */
    public function it_can_create_grids_with_a_doctrine_entity(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::PRICE_GRID_PATH));

        try {
            $tester->execute(['entity' => Price::class, '--namespace' => 'Tests\Tmp\Grid']);
        } finally {
            restore_exception_handler();
        }

        $this->assertFileExists(self::tempFile(self::PRICE_GRID_PATH));
        $this->assertSame(self::getPriceGridExpectedContent(), \file_get_contents(self::tempFile(self::PRICE_GRID_PATH)));
    }

    /** @test */
    public function it_can_create_grids_without_doctrine(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::BOARD_GAME_GRID_PATH));

        try {
            $tester->execute(['entity' => BoardGame::class, '--namespace' => 'Tests\Tmp\Grid']);
        } finally {
            restore_exception_handler();
        }

        $this->assertFileExists(self::tempFile(self::BOARD_GAME_GRID_PATH));
        $this->assertSame(self::getBoardGameGridExpectedContent(), \file_get_contents(self::tempFile(self::BOARD_GAME_GRID_PATH)));
    }

    /** @test */
    public function it_can_create_grids_with_boolean_fields(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::BOOK_GRID_PATH));

        try {
            $tester->execute(['entity' => Book::class, '--namespace' => 'Tests\Tmp\Grid']);
        } finally {
            restore_exception_handler();
        }

        $this->assertFileExists(self::tempFile(self::BOOK_GRID_PATH));
        $this->assertSame(self::getBookGridExpectedContent(), \file_get_contents(self::tempFile(self::BOOK_GRID_PATH)));
    }

    /** @test */
    public function it_uses_snake_case_names_for_grids(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::ADMIN_USER_GRID_PATH));

        try {
            $tester->execute(['entity' => AdminUser::class, '--namespace' => 'Tests\Tmp\Grid']);
        } finally {
            restore_exception_handler();
        }

        $this->assertFileExists(self::tempFile(self::ADMIN_USER_GRID_PATH));
        $this->assertSame(self::getAdminUserGridExpectedContent(), \file_get_contents(self::tempFile(self::ADMIN_USER_GRID_PATH)));
    }

    /** @test */
    public function it_can_create_grids_interactively(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile(self::ADMIN_USER_GRID_PATH));

        $tester->setInputs([AdminUser::class]);

        try {
            $tester->execute(['--namespace' => 'Tests\Tmp\Grid']);
        } finally {
            restore_exception_handler();
        }

        $this->assertFileExists(self::tempFile(self::ADMIN_USER_GRID_PATH));
        $this->assertSame(self::getAdminUserGridExpectedContent(), \file_get_contents(self::tempFile(self::ADMIN_USER_GRID_PATH)));
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
        } finally {
            restore_exception_handler();
        }

        $this->fail('Exception not thrown.');
    }

    private static function getBookGridExpectedContent(): string
    {
        return <<<'EOF'
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
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    resourceClass: Book::class,
    name: 'app_book',
)]
final class BookGrid extends AbstractGrid
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            // see https://stack.sylius.com/grid/index/filters
            // ->addFilters()
            // see https://stack.sylius.com/grid/index/field_types
            ->addFields(
                StringField::create('title')
                    ->setLabel('Title')
                    ->setSortable(true),
                StringField::create('state')
                    ->setLabel('State')
                    ->setSortable(true),
                //    TwigField::create('enabled', 'path/to/field/template.html.twig')
                //        ->setLabel('Enabled'),
                DateTimeField::create('createdAt')
                    ->setLabel('CreatedAt'),
                DateTimeField::create('updatedAt')
                    ->setLabel('UpdatedAt'),
                DateTimeField::create('publishedAt')
                    ->setLabel('PublishedAt'),
                StringField::create('price.currencyCode')
                    ->setLabel('Price.currencyCode')
                    ->setSortable(true),
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
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    resourceClass: Price::class,
    name: 'app_price',
)]
final class PriceGrid extends AbstractGrid
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public function __invoke(GridBuilderInterface \$gridBuilder): void
    {
        \$gridBuilder
            // see https://stack.sylius.com/grid/index/filters
            // ->addFilters()
            // see https://stack.sylius.com/grid/index/field_types
            ->addFields(
                StringField::create('currencyCode')
                    ->setLabel('CurrencyCode')
                    ->setSortable(true),
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
}

EOF
        ;
    }

    private static function getAdminUserGridExpectedContent(): string
    {
        return <<<EOF
<?php

namespace App\Tests\Tmp\Grid;

use App\Entity\AdminUser;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    resourceClass: AdminUser::class,
    name: 'app_admin_user',
)]
final class AdminUserGrid extends AbstractGrid
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public function __invoke(GridBuilderInterface \$gridBuilder): void
    {
        \$gridBuilder
            // see https://stack.sylius.com/grid/index/filters
            // ->addFilters()
            // see https://stack.sylius.com/grid/index/field_types
            ->addFields(
                StringField::create('username')
                    ->setLabel('Username')
                    ->setSortable(true),
                StringField::create('status')
                    ->setLabel('Status')
                    ->setSortable(true),
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
}

EOF
        ;
    }

    private static function getBoardGameGridExpectedContent(): string
    {
        return <<<EOF
<?php

namespace App\Tests\Tmp\Grid;

use App\BoardGameBlog\Domain\Model\BoardGame;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    resourceClass: BoardGame::class,
    name: 'app_board_game',
)]
final class BoardGameGrid extends AbstractGrid
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public function __invoke(GridBuilderInterface \$gridBuilder): void
    {
        \$gridBuilder
            // see https://stack.sylius.com/grid/index/filters
            // ->addFilters()
            // see https://stack.sylius.com/grid/index/field_types
            ->addFields(
                StringField::create('name')
                    ->setLabel('Name')
                    ->setSortable(true),
                StringField::create('shortDescription')
                    ->setLabel('ShortDescription')
                    ->setSortable(true),
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
}

EOF
        ;
    }
}
