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
