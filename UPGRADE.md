## UPGRADE FOR `1.16.x`

### FROM `1.15.x` TO `1.16.x`

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
