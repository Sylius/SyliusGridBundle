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

namespace Sylius\Bundle\GridBundle\Provider;

use Sylius\Bundle\GridBundle\Builder\GridBuilder;
use Sylius\Bundle\GridBundle\Grid\GridInterface;
use Sylius\Bundle\GridBundle\Registry\GridRegistryInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationExtenderInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationSortingHandlerInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\InvokableGridCollection;
use Sylius\Component\Grid\InvokableGridCollectionInterface;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Webmozart\Assert\Assert;

final class ServiceGridProvider implements GridProviderInterface
{
    private ArrayToDefinitionConverterInterface $converter;

    private GridRegistryInterface $gridRegistry;

    private GridConfigurationExtenderInterface $gridConfigurationExtender;

    private GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler;

    private GridConfigurationSortingHandlerInterface $gridConfigurationSortingHandler;

    private InvokableGridCollectionInterface $invokableGridCollection;

    public function __construct(
        ArrayToDefinitionConverterInterface $converter,
        GridRegistryInterface $gridRegistry,
        GridConfigurationExtenderInterface $gridConfigurationExtender,
        ?GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler = null,
        ?GridConfigurationSortingHandlerInterface $gridConfigurationSortingHandler = null,
        ?InvokableGridCollectionInterface $invokableGridCollection = null,
    ) {
        $this->converter = $converter;
        $this->gridRegistry = $gridRegistry;
        $this->gridConfigurationExtender = $gridConfigurationExtender;
        $this->gridConfigurationRemovalsHandler = $gridConfigurationRemovalsHandler ?? new GridConfigurationRemovalsHandler();
        $this->gridConfigurationSortingHandler = $gridConfigurationSortingHandler ?? new GridConfigurationSortingHandler();
        $this->invokableGridCollection = $invokableGridCollection ?? new InvokableGridCollection();
    }

    public function get(string $code): Grid
    {
        if (is_a($code, GridInterface::class, true)) {
            $code = $code::getName();
        }

        $grid = $this->gridRegistry->getGrid($code);

        if (null === $grid) {
            throw new UndefinedGridException($code);
        }

        $gridConfiguration = $grid->toArray();

        if (isset($gridConfiguration['extends'])) {
            /** @var string $parentGridCode */
            $parentGridCode = $gridConfiguration['extends'];
            $gridConfiguration = $this->extend($gridConfiguration, $parentGridCode);
        }

        $gridConfiguration = $this->gridConfigurationRemovalsHandler->handle($gridConfiguration);
        $gridConfiguration = $this->gridConfigurationSortingHandler->handle($gridConfiguration);

        return $this->converter->convert($code, $gridConfiguration);
    }

    /**
     * @param array<string, mixed> $gridConfiguration
     *
     * @return array<string, mixed>
     */
    private function extend(array $gridConfiguration, string $parentGridCode): array
    {
        /** @var GridInterface|callable $parentGrid */
        $parentGrid = $this->gridRegistry->getGrid($parentGridCode) ?? $this->invokableGridCollection->get($parentGridCode);

        Assert::notNull($parentGrid, sprintf('Parent grid with code "%s" does not exists.', $parentGridCode));

        return $this->gridConfigurationExtender->extends($gridConfiguration, $this->getParentGridConfiguration($parentGridCode, $parentGrid));
    }

    private function getParentGridConfiguration(string $parentGridCode, GridInterface|callable $parentGrid): array
    {
        if ($parentGrid instanceof GridInterface) {
            return $parentGrid->toArray();
        }

        $gridBuilder = GridBuilder::create($parentGridCode);
        $parentGrid($gridBuilder);

        return $gridBuilder->toArray();
    }
}
