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

namespace App\Controller;

use Pagerfanta\PagerfantaInterface;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Grid\View\GridViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\Attribute\Required;

trait GridTrait
{
    private Request $request;

    private GridViewFactoryInterface $gridViewFactory;

    private GridProviderInterface $gridProvider;

    public function getGridView(string $grid): GridViewInterface
    {
        $gridDefinition = $this->gridProvider->get($grid);

        $limit = $this->request->query->get('limit');
        $criteria = $this->request->query->all('criteria');
        $sorting = $this->request->query->all('sorting');

        $gridView = $this->gridViewFactory->create(
            $gridDefinition,
            new Parameters([
                'criteria' => $criteria,
                'sorting' => [] !== $sorting ? $sorting : $gridDefinition->getSorting(),
            ]),
        );

        $data = $gridView->getData();

        if ($data instanceof PagerfantaInterface) {
            if (null !== $limit) {
                $data->setMaxPerPage((int) $limit);
            }

            $data->setCurrentPage($this->request->query->getInt('page', 1));
        }

        return $gridView;
    }

    #[Required]
    public function setRequestStack(
        RequestStack $requestStack,
    ): void {
        $this->request = $requestStack->getCurrentRequest() ?? new Request();
    }

    #[Required]
    public function setGridProvider(
        GridProviderInterface $gridProvider,
    ): void {
        $this->gridProvider = $gridProvider;
    }

    #[Required]
    public function setGridViewFactory(GridViewFactoryInterface $gridViewFactory): void
    {
        $this->gridViewFactory = $gridViewFactory;
    }
}
