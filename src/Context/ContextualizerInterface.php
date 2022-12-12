<?php

declare(strict_types=1);

namespace Psl\Sentry\Context;

use Sentry\Event;
use Sentry\EventHint;

interface ContextualizerInterface
{
    /**
     * @return non-empty-string
     */
    public static function contextName(): string;

    public function addContext(Event $event, EventHint $hint): void;
}
