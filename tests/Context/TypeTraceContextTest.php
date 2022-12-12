<?php

declare(strict_types=1);

namespace Psl\Sentry\Tests\Context;

use PHPUnit\Framework\TestCase;
use Psl\Sentry\Context\TypeTraceContextualizer;
use Psl\Type\Exception\CoercionException;
use Psl\Type\Exception\TypeTrace;
use Sentry\Event;
use Sentry\EventHint;

class TypeTraceContextTest extends TestCase
{
    public function testContextName(): void
    {
        static::assertSame('Psl\Type', TypeTraceContextualizer::contextName());
    }

    public function testAddContext(): void
    {
        $contextualizer = new TypeTraceContextualizer();

        $event = Event::createEvent();
        $hint = EventHint::fromArray([
            'exception' => CoercionException::withValue(
                'foo',
                'null',
                (new TypeTrace())
                    ->withFrame('frame 1')
                    ->withFrame('frame 2')
            ),
        ]);

        static::assertSame([], $event->getContexts(), 'The event contexts are not clean');

        $contextualizer->addContext($event, $hint);

        static::assertSame(
            [
                'Psl\Type' => [
                    'trace' => [
                        'frame 1',
                        'frame 2',
                    ]
                ]
            ],
            $event->getContexts(),
            'Type traces have not been added to Sentry\'s context'
        );
    }
}
