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

namespace Sylius\Bundle\GridBundle\Tests\Functional\Twig\Component;

use App\Entity\Author;
use App\Twig\EntityFilterComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Twig\Component\ComponentWithEntityFilterTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(ComponentWithEntityFilterTrait::class)]
final class ComponentWithEntityFilterTraitTest extends KernelTestCase
{
    use InteractsWithLiveComponents;
    use Factories;
    use ResetDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Emulate a session
        $session = new Session(new MockArraySessionStorage());
        $session->start();

        $request = Request::create('/');
        $request->setSession($session);

        self::getContainer()->get('request_stack')->push($request);
    }

    public function testRender(): void
    {
        $filter = Filter::fromNameAndType('author', 'entity');
        $filter->setFormOptions(['class' => Author::class]);

        $component = $this->createLiveComponent(
            name: EntityFilterComponent::class,
            data: [
                'filter' => $filter,
            ],
        );

        $this->assertStringContainsString(<<<'HTML'
            <label for="criteria_author" class="required">author</label><select id="criteria_author" name="criteria[author]" required="required" data-model="value" data-action="live#action" data-live-action-param="onChange"><option value="" selected="selected">All</option></select>
            HTML, $component->render()->toString());
    }

    public function testEmitFilterChangedEvent(): void
    {
        $filter = Filter::fromNameAndType('author', 'entity');
        $filter->setFormOptions(['class' => Author::class]);

        $component = $this->createLiveComponent(
            name: EntityFilterComponent::class,
            data: [
                'filter' => $filter,
            ],
        );

        $component->call('onChange');

        $this->assertComponentEmitEvent($component, 'filterChanged');
    }
}
