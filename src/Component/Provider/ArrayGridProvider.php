<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Grid\Provider;

use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Component\Grid\Configuration\GridConfigurationExtender;
use Sylius\Component\Grid\Configuration\GridConfigurationExtenderInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandlerInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Mutator\GridMutatorCollection;
use Sylius\Component\Grid\Mutator\GridMutatorCollectionInterface;
use Webmozart\Assert\Assert;

final class ArrayGridProvider implements GridProviderInterface
{
    private ArrayToDefinitionConverterInterface $converter;

    private GridConfigurationExtenderInterface $gridConfigurationExtender;

    private GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler;

    private GridConfigurationSortingHandlerInterface $gridConfigurationSortingHandler;

    private GridMutatorCollectionInterface $gridMutatorCollection;

    /** @var array<string, array<string, mixed>> */
    private array $gridConfigurations;

    /**
     * @param array<string, array<string, mixed>> $gridConfigurations
     */
    public function __construct(
        ArrayToDefinitionConverterInterface $converter,
        array $gridConfigurations,
        ?GridConfigurationExtenderInterface $gridConfigurationExtender = null,
        ?GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler = null,
        ?GridConfigurationSortingHandlerInterface $gridConfigurationSortingHandler = null,
        ?GridMutatorCollectionInterface $gridMutatorCollection = null,
    ) {
        $this->converter = $converter;
        $this->gridConfigurations = $gridConfigurations;
        $this->gridConfigurationExtender = $gridConfigurationExtender ?? new GridConfigurationExtender();
        $this->gridConfigurationRemovalsHandler = $gridConfigurationRemovalsHandler ?? new GridConfigurationRemovalsHandler();
        $this->gridConfigurationSortingHandler = $gridConfigurationSortingHandler ?? new GridConfigurationSortingHandler();
        $this->gridMutatorCollection = $gridMutatorCollection ?? new GridMutatorCollection();
    }

    public function get(string $code): Grid
    {
        if (!array_key_exists($code, $this->gridConfigurations)) {
            throw new UndefinedGridException($code);
        }

        $gridConfiguration = $this->getGridConfiguration($code);

        /** @var string|null $parentGridCode */
        $parentGridCode = $gridConfiguration['extends'] ?? null;

        if (null !== $parentGridCode) {
            $parentGridConfiguration = $this->getGridConfiguration($parentGridCode);
            $gridConfiguration = $this->gridConfigurationExtender->extends($gridConfiguration, $parentGridConfiguration);
        }

        $gridConfiguration = $this->gridConfigurationRemovalsHandler->handle($gridConfiguration);
        $gridConfiguration = $this->gridConfigurationSortingHandler->handle($gridConfiguration);

        return $this->converter->convert($code, $gridConfiguration);
    }

    /**
     * @return array<string, mixed>
     */
    private function getGridConfiguration(string $code): array
    {
        /** @var array{
         *      driver: array{
         *          name: string,
         *          options: array<string, mixed>
         *      },
         *  } $gridConfiguration */
        $gridConfiguration = $this->gridConfigurations[$code] ?? null;

        Assert::notNull($gridConfiguration, sprintf('Grid with code "%s" does not exists.', $code));

        $driver = $gridConfiguration['driver']['name'] ?? null;
        $driverOptions = $gridConfiguration['driver']['options'] ?? [];

        $gridBuilder = GridBuilder::create($code);

        if (null !== $driver) {
            $gridBuilder->setDriver($driver);
        }

        foreach ($driverOptions as $option => $value) {
            $gridBuilder->setDriverOption($option, $value);
        }

        foreach ($this->gridMutatorCollection->get($code) as $mutator) {
            ($mutator)($gridBuilder);
        }

        /** @var array<string, mixed> $builderConfiguration */
        $builderConfiguration = $gridBuilder->toArray();

        /** @var array<string, mixed> $builderRemovals */
        $builderRemovals = $builderConfiguration['removals'] ?? [];

        if ([] !== $builderRemovals) {
            $builderConfiguration['removals'] = $this->mergeRemovals(
                $gridConfiguration['removals'] ?? [],
                $builderRemovals,
            );
        }

        if (isset($builderConfiguration['sorting'])) {
            /** @phpstan-ignore unset.offset */
            unset($gridConfiguration['sorting']);
        }

        /** @var array<string, mixed> $newGridConfiguration */
        $newGridConfiguration = array_replace_recursive($gridConfiguration, $builderConfiguration);

        return $newGridConfiguration;
    }

    /**
     * Removals contain numeric lists (flat for fields/filters, nested per group for actions) —
     * array_replace_recursive would collide their indexes, so union them instead.
     *
     * @param array<array-key, mixed> $existing
     * @param array<array-key, mixed> $new
     *
     * @return array<array-key, mixed>
     */
    private function mergeRemovals(array $existing, array $new): array
    {
        foreach ($new as $key => $value) {
            if (is_int($key)) {
                if (!in_array($value, $existing, true)) {
                    $existing[] = $value;
                }
            } elseif (is_array($value)) {
                /** @var array<array-key, mixed> $subExisting */
                $subExisting = $existing[$key] ?? [];
                /** @var array<array-key, mixed> $subValue */
                $subValue = $value;
                $existing[$key] = $this->mergeRemovals($subExisting, $subValue);
            } else {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }
}
