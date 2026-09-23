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

use App\Twig\StringFilterComponent;
use PHPUnit\Framework\Attributes\CoversClass;
use Sylius\Component\Grid\Definition\Filter;
use Sylius\Component\Grid\Twig\Component\ComponentWithStringFilterTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\UX\LiveComponent\Test\InteractsWithLiveComponents;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(ComponentWithStringFilterTrait::class)]
final class ComponentWithStringFilterTraitTest extends KernelTestCase
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
        $component = $this->createLiveComponent(
            name: StringFilterComponent::class,
            data: [
                'filter' => Filter::fromNameAndType('search', 'string'),
            ],
        );

        $this->assertStringContainsString(<<<'HTML'
            <label for="criteria_search_value">Value</label><input type="text" id="criteria_search_value" name="criteria[search][value]" placeholder="Value" data-action="keyup-&gt;live#action" data-live-action-param="onChange" data-model="value" data-model-debounce="300" />
            HTML, $component->render()->toString());
    }

    public function testEmitFilterChangedEvent(): void
    {
        $component = $this->createLiveComponent(
            name: StringFilterComponent::class,
            data: [
                'filter' => Filter::fromNameAndType('search', 'string'),
            ],
        );

        $component->call('onChange');

        $this->assertComponentEmitEvent($component, 'filterChanged');
    }
}
