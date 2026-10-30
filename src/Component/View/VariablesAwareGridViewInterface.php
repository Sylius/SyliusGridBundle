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

namespace Sylius\Component\Grid\View;

/**
 * A grid view that knows the variables of the page displaying it (typically its route parameters),
 * so that its configuration can be resolved without any request:
 *
 *  - "$productId" is replaced by the "productId" variable;
 *  - "@=..." expressions get them as "variables", e.g. "@=variables['productId']".
 *
 * The variables are the same for the regular and the live grids, so that a grid configuration works in both cases:
 *
 *  - regular grids: the integration fills them from the request (e.g. the request attributes and query
 *    in sylius/resource-bundle);
 *  - live grids: the request is the live component one during the re-renders, so the variables are captured
 *    when the component is mounted and kept as a LiveProp (see ComponentWithGridTrait::$gridVariables).
 *
 * That is why "variables['slug']" should be preferred to "request.attributes.get('slug')" in the grid expressions.
 *
 * @experimental
 */
interface VariablesAwareGridViewInterface extends GridViewInterface
{
    /** @return array<string, mixed> */
    public function getVariables(): array;
}
