<?php declare(strict_types=1);

use Symfony\Bundle\MakerBundle\Str;

$defaultFields = is_array($defaultFields) ? $defaultFields : iterator_to_array($defaultFields);
$hasDateTimeFields = false;
$hasBooleanFields = false;

foreach ($defaultFields as $type) {
    $type = strtoupper((string) $type);

    if (str_starts_with($type, 'DATE')) {
        $hasDateTimeFields = true;
    }

    if (in_array($type, ['BOOLEAN', 'BOOL'], true)) {
        $hasBooleanFields = true;
    }
}

?>
<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $entity->getName() ?>;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
<?php if ($hasDateTimeFields): ?>
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
<?php endif; ?>
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
<?php if ($hasBooleanFields): ?>
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
<?php endif; ?>
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Builder\GridBuilderInterface;

#[AsGrid(
    resourceClass: <?= $entity->getShortName() ?>::class,
    name: 'app_<?= Str::asSnakeCase(($entity->getShortName())) ?>',
)]
final class <?= $class_name ?><?= "\n" ?>{
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
                        echo "                //    TwigField::create('" . $fieldname . "', 'path/to/field/template.html.twig')\n";
                        echo "                //        ->setLabel('" . ucfirst($fieldname) . "'),\n";
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
