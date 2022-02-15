Custom Field Type
=================

There are certain cases when built-in field types are not enough. Sylius
Grids allows to define new types with ease!

All you need to do is create your own class implementing
FieldTypeInterface and register it as a service.

```php
<?php

namespace App\Grid\FieldType;

use Sylius\Component\Grid\Definition\Field;
use Sylius\Component\Grid\FieldTypes\FieldTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomType implements FieldTypeInterface
{
    public function render(Field $field, $data, array $options = [])
    {
        // Your rendering logic... Use Twig, PHP or even external api...
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'dynamic' => false
            ])
            ->setAllowedTypes([
                'dynamic' => ['boolean']
            ])
        ;
    }

    public function getName(): string
    {
        return 'custom';
    }
}
```

That is all. Now register your new field type as a service.

```yaml
# config/services.yaml
app.grid_field.custom:
    class: App\Grid\FieldType\CustomType
    tags:
        - { name: sylius.grid_field, type: custom }
```

Now you can use your new column type in the grid configuration!

<details open><summary>Yaml</summary>

```yaml
sylius_grid:
    grids:
        app_admin_supplier:
            driver:
                name: doctrine/orm
                options:
                    class: App\Entity\Supplier
            fields:
                name:
                    type: custom
                    label: sylius.ui.name
```

</details>

<details open><summary>PHP</summary>

```php
<?php

use App\Entity\Suplier;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_admin_supplier', Suplier::class)
        ->addField(
            Field::create('name', 'custom')
                ->setLabel('sylius.ui.name')
        )
    )
};
```

</details>
