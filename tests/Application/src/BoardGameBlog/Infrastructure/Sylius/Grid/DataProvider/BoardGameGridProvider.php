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

namespace App\BoardGameBlog\Infrastructure\Sylius\Grid\DataProvider;

use App\BoardGameBlog\Infrastructure\Sylius\Resource\BoardGameResource;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Grid\Data\DataProviderInterface;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Webmozart\Assert\Assert;

final class BoardGameGridProvider implements DataProviderInterface
{
    public function __construct(private string $dataDir)
    {
    }

    public function getData(Grid $grid, Parameters $parameters): Pagerfanta
    {
        $data = [];

        foreach ($this->getFileData() as $row) {
            [$id, $name, $shortDescription] = str_getcsv($row);

            Assert::notNull($id);
            Assert::notNull($name);
            Assert::notNull($shortDescription);

            $data[] = new BoardGameResource(
                id: $id,
                name: $name,
                shortDescription: $shortDescription,
            );
        }

        return new Pagerfanta(new ArrayAdapter($data));
    }

    private function getFileData(): array
    {
        return file($this->dataDir . '/board_games.csv');
    }
}
