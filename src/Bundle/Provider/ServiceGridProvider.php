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

use Sylius\Bundle\GridBundle\Registry\GridRegistryInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationExtenderInterface;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandler;
use Sylius\Component\Grid\Configuration\GridConfigurationRemovalsHandlerInterface;
use Sylius\Component\Grid\Definition\ArrayToDefinitionConverterInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Exception\UndefinedGridException;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Webmozart\Assert\Assert;

final class ServiceGridProvider implements GridProviderInterface
{
    private ArrayToDefinitionConverterInterface $converter;

    private GridRegistryInterface $gridRegistry;

    private GridConfigurationExtenderInterface $gridConfigurationExtender;

    private GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler;

    public function __construct(
        ArrayToDefinitionConverterInterface $converter,
        GridRegistryInterface $gridRegistry,
        GridConfigurationExtenderInterface $gridConfigurationExtender,
        ?GridConfigurationRemovalsHandlerInterface $gridConfigurationRemovalsHandler = null,
    ) {
        $this->converter = $converter;
        $this->gridRegistry = $gridRegistry;
        $this->gridConfigurationExtender = $gridConfigurationExtender;
        $this->gridConfigurationRemovalsHandler = $gridConfigurationRemovalsHandler ?? new GridConfigurationRemovalsHandler();
    }

    public function get(string $code): Grid
    {
        $grid = $this->gridRegistry->getGrid($code);

        if (null === $grid) {
            throw new UndefinedGridException($code);
        }

        $gridConfiguration = $grid->toArray();

        if (isset($gridConfiguration['extends'])) {
            $gridConfiguration = $this->extend($gridConfiguration, $gridConfiguration['extends']);
        }

        $gridConfiguration = $this->gridConfigurationRemovalsHandler->handle($gridConfiguration);

        return $this->converter->convert($code, $gridConfiguration);
    }

    private function extend(array $gridConfiguration, string $parentGridCode): array
    {
        $parentGrid = $this->gridRegistry->getGrid($parentGridCode);

        Assert::notNull($parentGrid, sprintf('Parent grid with code "%s" does not exists.', $parentGridCode));

        $parentGridConfiguration = $parentGrid->toArray();

        return $this->gridConfigurationExtender->extends($gridConfiguration, $parentGridConfiguration);
    }
}
