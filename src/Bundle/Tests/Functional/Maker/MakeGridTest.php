<?php

declare(strict_types=1);

namespace Sylius\Bundle\GridBundle\Tests\Functional\Maker;

use App\Entity\Price;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

final class MakeGridTest extends MakerTestCase
{
    /**
     * @test
     */
    public function it_can_create_grids(): void
    {
        $tester = new CommandTester((new Application(self::bootKernel()))->find('make:grid'));

        $this->assertFileDoesNotExist(self::tempFile('Grid/PriceGrid.php'));

        $tester->execute(['entity' => Price::class, '--namespace' => 'Tests\Tmp\Grid']);

        $this->assertFileExists(self::tempFile('Grid/PriceGrid.php'));
        $this->assertSame(<<<EOF
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
            , \file_get_contents(self::tempFile('Grid/PriceGrid.php'))
        );
    }
}
