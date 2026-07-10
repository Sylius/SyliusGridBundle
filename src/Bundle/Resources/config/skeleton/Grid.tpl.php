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
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Component\Grid\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

#[AsGrid(
    resourceClass: <?= $entity->getShortName() ?>::class,
    name: 'app_<?= Str::asSnakeCase(($entity->getShortName())) ?>',
)]
final class <?= $class_name ?>
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            // see https://stack.sylius.com/grid/index/filters
            // ->withFilters()
            // see https://stack.sylius.com/grid/index/field_types
            ->withFields(
<?php
                foreach ($defaultFields as $fieldname => $type) {
                    if (in_array($type, ['STRING', 'TEXT'], true)) {
                        echo "                StringField::create('" . $fieldname . "')\n";
                        echo "                    ->setLabel('" . ucfirst($fieldname) . "')\n";
                        echo "                    ->setSortable(true),\n";
                    }

                    if (str_starts_with($type, 'DATE')) {
                        echo "                DateTimeField::create('" . $fieldname . "')\n";
                        echo "                    ->setLabel('" . ucfirst($fieldname) . "'),\n";
                    }

                    if (in_array($type, ['BOOLEAN', 'BOOL'], true)) {
                        echo "            //    TwigField::create('" . $fieldname . "', 'path/to/field/template.html.twig')\n";
                        echo "            //        ->setLabel('" . ucfirst($fieldname) . "'),\n";
                    }
                }
?>
            )
            ->withMainActions(
                CreateAction::create(),
            )
            ->withItemActions(
                // ShowAction::create(),
                UpdateAction::create(),
                DeleteAction::create(),
            )
            ->withBulkActions(
                DeleteAction::create(),
            )
        ;
    }
}
