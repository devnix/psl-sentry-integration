<?php

declare(strict_types=1);

namespace Psl\Sentry\Context;

use Sentry\Event;
use Sentry\EventHint;

class TypeTraceContextualizer implements ContextualizerInterface
{
    public static function contextName(): string
    {
        return 'Psl\Type';
    }

    public function addContext(Event $event, EventHint $hint): void
    {
        $exception = $hint->exception;

        if (!$exception instanceof \Psl\Type\Exception\Exception) {
            return;
        }

        $event->setContext(self::contextName(), [
            'trace' => $exception->getTypeTrace()->getFrames(),
        ]);
    }
}
