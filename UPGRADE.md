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
