<?php declare(strict_types=1);

use Symfony\Bundle\MakerBundle\Str;

?>
<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $entity->getName() ?>;
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
    resourceClass: <?= $entity->getShortName() ?>::class,
    name: 'app_<?= Str::asSnakeCase(($entity->getShortName())) ?>',
)]
final class <?= $class_name ?> extends AbstractGrid
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
<?php
foreach ($defaultFields as $fieldname => $type) {
    if (in_array($type, ['STRING', 'TEXT'], true)) {
        echo "            ->addField(\n";
        echo "                StringField::create('" . $fieldname . "')\n";
        echo "                    ->setLabel('" . ucfirst($fieldname) . "')\n";
        echo "                    ->setSortable(true)\n";
        echo "            )\n";
    }

    if (str_starts_with($type, 'DATE')) {
        echo "            ->addField(\n";
        echo "                DateTimeField::create('" . $fieldname . "')\n";
        echo "                    ->setLabel('" . ucfirst($fieldname) . "')\n";
        echo "            )\n";
    }

    if (in_array($type, ['BOOLEAN', 'BOOL'], true)) {
        echo "            //->addField(\n";
        echo "            //    TwigField::create('" . $fieldname . "', 'path/to/field/template.html.twig')\n";
        echo "            //        ->setLabel('" . ucfirst($fieldname) . "')\n";
        echo "            //)\n";
    }
}
?>
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
