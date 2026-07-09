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

namespace Sylius\Bundle\GridBundle\Grid;

use Sylius\Component\Grid\Builder\GridBuilderInterface;
use Sylius\Component\Grid\Exception\LogicException;
use Sylius\Component\Grid\GridInterface;

/**
 * @internal
 */
final class InvokableGrid implements GridInterface
{
    /** @var callable */
    private $code;

    public function __construct(
        object|callable $code,
        private readonly string $name,
        private readonly ?string $class = null,
        private readonly ?string $resourceClass = null,
        public readonly ?string $buildMethod = null,
        private readonly ?string $provider = null,
    ) {
        if (!is_callable($code)) {
            $code = [$code, $this->buildMethod];
        }

        if (!is_callable($code)) {
            throw new LogicException('Your grid should be a callable.');
        }

        $this->code = $code;
    }

    public function __invoke(GridBuilderInterface $builder): void
    {
        ($this->code)($builder);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getResourceClass(): ?string
    {
        return $this->resourceClass;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }
}
