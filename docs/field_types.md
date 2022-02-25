Field Types
===========

This is the list of built-in field types.

String
------

Simplest column type, which basically renders the value at given path as
a string.

By default it uses the name of the field, but you can specify the path
alternatively. For example:

<details open><summary>Yaml</summary>

```yaml
# config/packages/sylius_grid.yaml

sylius_grid:
    grids:
        app_user:
            fields:
                email:
                    type: string
                    label: app.ui.email # each filed type can have a label, we suggest using translation keys instead of messages
                    path: contactDetails.email
```

</details>

<details open><summary>PHP</summary>

```php
<?php
// config/packages/sylius_grid.php

use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->addField(
            StringField::create('email')
                ->setLabel('app.ui.email') // # each filed type can have a label, we suggest using translation keys instead of messages
                ->setPath('contactDetails.email')
        )
    )
};
```

OR

```php
<?php
# src/Grid/UserGrid.php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                StringField::create('email')
                    ->setLabel('app.ui.email') // # each filed type can have a label, we suggest using translation keys instead of messages
                    ->setPath('contactDetails.email')
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```

</details>

This configuration will display the value from
`$user->getContactDetails()->getEmail()`.

DateTime
--------

This column type works exactly the same way as *string*, but expects
*DateTime* instance and outputs a formatted date and time string.

<details open><summary>Yaml</summary>

```yaml
# config/packages/sylius_grid.yaml

sylius_grid:
    grids:
        app_user:
            fields:
                birthday:
                    type: datetime
                    label: app.ui.birthday
                    options:
                        format: 'Y:m:d H:i:s' # this is the default value, but you can modify it
```

</details>

<details open><summary>PHP</summary>

```php
<?php
// config/packages/sylius_grid.php

use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->addField(
            DateTimeField::create('birthday', 'Y:m:d H:i:s') // this format is the default value, but you can modify it
                ->setLabel('app.ui.birthday')
        )
    )
};
```

OR

```php
<?php
# src/Grid/UserGrid.php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                DateTimeField::create('birthday', 'Y:m:d H:i:s') // this format is the default value, but you can modify it
                    ->setLabel('app.ui.birthday')
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```

</details>

Twig (*twig*)
-------------

Twig column type is the most flexible from all of them, because it
delegates the logic of rendering the value to Twig templating engine.
You just have to specify the template and it will be rendered with the
`data` variable available to you.

<details open><summary>Yaml</summary>

```yaml
# config/packages/sylius_grid.yaml

sylius_grid:
    grids:
        app_user:
            fields:
                name:
                    type: twig
                    label: app.ui.name
                    options:
                        template: "@Grid/Column/_prettyName.html.twig"
```

</details>

<details open><summary>PHP</summary>

```php
<?php
// config/packages/sylius_grid.php

use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Config\GridConfig;

return static function (GridConfig $grid) {
    $grid->addGrid(GridBuilder::create('app_user', '%app.model.user.class%')
        ->addField(
            TwigField::create('name', '@Grid/Column/_prettyName.html.twig')
                ->setLabel('app.ui.name')
        )
    )
};
```

OR

```php
<?php
# src/Grid/UserGrid.php

declare(strict_types=1);

namespace App\Grid;

use App\Entity\User;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class UserGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
           return 'app_user';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addField(
                TwigField::create('name', ':Grid/Column:_prettyName.html.twig')
                    ->setLabel('app.ui.name')
            )
        ;    
    }
    
    public function getResourceClass(): string
    {
        return User::class;
    }
}
```

</details>

In the `@Grid/Column/_prettyName.html.twig` template, you just need to
render the value for example as you see below:

```twig
<strong>{{ data }}</strong>
```

If you wish to render more complex grid fields just redefine the path of
the field to root – `path: .` in the yaml and you can access all
attributes of the object instance:

```twig
<strong>{{ data.name }}</strong>
<p>{{ data.description|markdown }}</p>
```
