<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Filter;

use App\Grid\Type\NationalityFilterType;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filter\EntityFilter;
use Sylius\Component\Grid\Filtering\ConfigurableFilterInterface;

final class NationalityFilter implements ConfigurableFilterInterface
{
    public function __construct(private EntityFilter $decorated)
    {
    }

    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $this->decorated->apply($dataSource, $name, $data, $options);
    }

    public static function getType(): string
    {
        return 'nationality';
    }

    public static function getFormType(): string
    {
        return NationalityFilterType::class;
    }
}
