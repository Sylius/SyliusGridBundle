Advanced Configuration
======================

By default, Doctrine option `fetchJoinCollection` and `useOutputWalkers` are enabled in all grids, but you can simply disable it with this config:

<details open><summary>Yaml</summary>

```yaml
sylius_grid:
    grids:
        foo:
            driver:
                options:
                    pagination:                
                        fetch_join_collection: false
                        use_output_walkers: false
```

</details>

<details open><summary>PHP</summary>

```php
<?php

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid): void {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->setDriverOption('pagination', [
            'fetch_join_collection' => false,
            'use_output_walkers' => false,
        ])
    )
};
```

</details>

These changes may be necessary when you work with huge databases.
