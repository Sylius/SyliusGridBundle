Advanced Configuration
======================

By default, Doctrine option `fetchJoinCollection` and `useOutputWalkers` are enabled in all grids, but you can simply disable it with this config:

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

```php
<?php

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->setDriverOption('pagination', [
            'fetch_join_collection' => false,
            'use_output_walkers' => false,
        ])
    )
};
```

These changes may be necessary when you work with huge databases.
