# CONFLICTS

This document explains why certain conflicts were added to `composer.json` and
references related issues.

## Pagerfanta 3 

Pagerfanta 3 is not supported yet due to the usage of deprecated `Pagerfanta\Adapter\DoctrineORMAdapter`. 
 
* `Sylius\Bundle\GridBundle\Doctrine\ORM\DataSource` 
* `Sylius\Bundle\GridBundle\Doctrine\DBAL\DataSource`

So we added these 4 conflicts. 
These conflicts will be removed in the next release.

* "pagerfanta/doctrine-dbal-adapter": ">=3.0"
* "pagerfanta/doctrine-orm-adapter": ">=3.0"
* "pagerfanta/doctrine-phpcr-odm-adapter": ">=3.0"
* "pagerfanta/pagerfanta": ">=3.0"
