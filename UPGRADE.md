## UPGRADE FOR `1.16.x`

### FROM `1.15.x` TO `1.16.x`

### Grid builder interfaces

A few interfaces have been moved:

| Before                                                              | After                                                            |
|---------------------------------------------------------------------|------------------------------------------------------------------|
| `Sylius\Bundle\GridBundle\Builder\Action\ActionInterface`           | `Sylius\Component\Grid\Builder\Action\ActionInterface`           |
| `Sylius\Bundle\GridBundle\Builder\ActionGroup\ActionGroupInterface` | `Sylius\Component\Grid\Builder\ActionGroup\ActionGroupInterface` |
| `Sylius\Bundle\GridBundle\Builder\Field\FieldInterface`             | `Sylius\Component\Grid\Builder\Field\FieldInterface`             |
| `Sylius\Bundle\GridBundle\Builder\Filter\FilterInterface`           | `Sylius\Component\Grid\Builder\Filter\FilterInterface`           |
| `Sylius\Bundle\GridBundle\Builder\GridBuilderInterface`             | `Sylius\Component\Grid\Builder\GridBuilderInterface`             |

The previous interfaces still be there for bc-layer purpose, but they are deprecated, please use the new ones instead.

#### PHP Grids

The `Sylius\Bundle\GridBundle\Grid\AbstractGrid` and the `Sylius\Bundle\GridBundle\Grid\GridInterface` are deprecated.
You should use the `Sylius\Component\Grid\Attribute\AsGrid` attribute only.

```diff
<?php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\Book;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Component\Grid\Attribute\AsGrid;

+#[AsGrid(resourceClass: Book::class, name: 'app_book')]
-final class BookGrid extends AbstractGrid implements ResourceAwareGridInterface
+final class BookGrid
{
-    public function buildGrid(GridBuilderInterface $gridBuilder): void
+    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        // ...
    }
-    
-    public function getResourceClass(): string
-    {
-        return Book::class;
-    }
-
-    public static function getName(): string
-    {
-        return 'app_book'
-    }
}

```

#### YAML Grids

Using YAML to configure Grids is deprecated, please convert your Grids into PHP ones using the [Grid converter](https://github.com/mamazu/grid-config-converter).

**Before**
```yaml
sylius_grid:
    grids:
        app_admin_book:
            driver:
                name: doctrine/orm
                options:
                    class: App\Entity\Book
            fields: 
                title: 
                    type: string
                    label: app.ui.title
```

**After**

```php
<?php

namespace App\Grid;

use App\Entity\Book;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Builder\GridBuilderInterface;

#[AsGrid(
    name: 'app_admin_book',
    resourceClass: Book::class
)]
final class AdminBookGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
         $gridBuilder
            ->withFields(
                StringField::create('title')
                    ->setLabel('app.ui.title'),
            )
         ;
    }
}
```

The `Sylius\Component\Grid\Provider\ArrayGridProvider` is deprecated. This provider is responsible to convert a YAML grid into a Grid definition.

#### PHP Grids within configuration files

Configuring Grids in Symfony configuration files is deprecated, please move your configuration files into PHP objects using the `AsGrid` attribute.

**Before**
```php
<?php 

use App\Entity\Book;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_admin_book', Book::class)
        ->withFields(
            StringField::create('title')
                ->setLabel('app.ui.title'),  
        )
    )
};
```

**after**

```php
<?php

namespace App\Grid;

use App\Entity\Book;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Component\Grid\Attribute\AsGrid;
use Sylius\Component\Grid\Builder\GridBuilderInterface;

#[AsGrid(
    name: 'app_admin_book',
    resourceClass: Book::class
)]
final class AdminBookGrid
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
         $gridBuilder
            ->withFields(
                StringField::create('title')
                    ->setLabel('app.ui.title'),
            )
         ;
    }
}
```

#### Grid events

Grid events are deprecated, please use Grid mutators instead.

**Before**
```php
namespace App\Grid;

use Sylius\Component\Grid\Event\GridDefinitionConverterEvent;
use Sylius\Component\Grid\Definition\Field;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'sylius.grid.admin_product', method: 'editFields')]
final class RemoveImageFromProductGridListener
{
    public function editFields(GridDefinitionConverterEvent $event): void
    {
        $grid = $event->getGrid();

        $grid->removeField('image');
    }
}
```

**After**

```php
<?php

namespace App\Grid\Mutator;

use Sylius\Component\Grid\Attribute\AsGridMutator;
use Sylius\Component\Grid\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Mutator\GridMutatorInterface;

#[AsGridMutator(
    grid: 'sylius_admin_product', 
)]
class RemoveImageFromProductGridMutator implements GridMutatorInterface
{
    public function __invoke(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder->removeField('image');
    }
}
```

## UPGRADE FOR `1.15.x`

### FROM `1.14.x` TO `1.15.x`

#### General

1. The minimum version of Symfony 7 packages has been bumped from Symfony `^7.0` to `^7.2`
2. The minimum version of PHP has been bumped from PHP `^8.1` to `^8.2`

## UPGRADE FOR `1.11.x`

### FROM `1.10.x` TO `1.11.x`

#### Grid inheritance

The parent grid now should exist when using grid inheritance.

Example
```yaml
sylius_grid:
    grids:
        book:
            extends: product
```

Then the `product` grid should exist.
