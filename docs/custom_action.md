Custom Action
=============

There are certain cases when built-in action types are not enough.

All you need to do is create your own action template and register it
for the `sylius_grid`.

In the template we will specify the button's icon to be `mail` and its
colour to be `purple`.

```twig
{% import '@SyliusUi/Macro/buttons.html.twig' as buttons %}

{% set path = options.link.url|default(path(options.link.route, options.link.parameters)) %}

{{ buttons.default(path, action.label, null, 'mail', 'purple') }}
```

Now configure the new action's template like below in the
`config/packages/sylius_grid.yaml`:

```yaml
# config/packages/sylius_grid.yaml
sylius_grid:
    templates:
        action:
            contactSupplier: "@App/Grid/Action/contactSupplier.html.twig"
```

From now on you can use your new action type in the grid configuration!

Let's assume that you already have a route for contacting your
suppliers, then you can configure the grid action:

<details open><summary>Yaml</summary>

```yaml
# config/packages/sylius_grid.yaml

sylius_grid:
    grids:
        app_admin_supplier:
            driver:
                name: doctrine/orm
                options:
                    class: App\Entity\Supplier
            actions:
                item:
                    contactSupplier:
                        type: contactSupplier
                        label: Contact Supplier
                        options:
                            link:
                                route: app_admin_contact_supplier
                                parameters:
                                    id: resource.id
```

</details>

<details open><summary>PHP</summary>

```php
<?php
// config/packages/sylius_grid.php

use App\Entity\Supplier;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_admin_supplier', Supplier::class)
        ->addActionGroup(
            ItemActionGroup::create(
                Action::create('contactSupplier', 'contactSupplier')
                    ->setLabel('Contact Supplier')
                    ->setOptions([
                        'link' => [
                            'route' => 'app_admin_contact_supplier',
                            'parameters' => [
                                'id' => 'resource.id',
                            ],
                        ],
                    ])
            )
        ])
    )
};
```

OR

```php
<?php
# src/Grid/AdminSupplierGrid.php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\Supplier;
use Sylius\Bundle\GridBundle\Builder\Action\Action;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class AdminSupplierGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_admin_supplier';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addActionGroup(
                ItemActionGroup::create(
                    Action::create('contactSupplier', 'contactSupplier')
                        ->setLabel('Contact Supplier')
                        ->setOptions([
                            'link' => [
                                'route' => 'app_admin_contact_supplier',
                                'parameters' => [
                                    'id' => 'resource.id',
                                ],
                            ],
                        ])
                )
            ])
        ;    
    }
    
    public function getResourceClass(): string
    {
        return Supplier::class;
    }
}
```

</details>
