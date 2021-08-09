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

These changes may be necessary when you work with huge databases.
