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

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Sylius\Bundle\GridBundle\SyliusGridBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;

return [
    FrameworkBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true, 'test_without_doctrine' => false],
    TwigBundle::class => ['all' => true],
    SyliusResourceBundle::class => ['all' => true],
    SyliusGridBundle::class => ['all' => true],
    BabDevPagerfantaBundle::class => ['all' => true],
    MakerBundle::class => ['all' => true, 'test_without_maker' => false],
    Zenstruck\Foundry\ZenstruckFoundryBundle::class => ['all' => true],
];
