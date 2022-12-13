<?php

declare(strict_types=1);

namespace Psl\Sentry;

use Psl\Sentry\Context\ContextualizerInterface;
use Psl\Sentry\Context\TypeTraceContextualizer;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\Integration\IntegrationInterface;
use Sentry\SentrySdk;
use Sentry\State\Scope;

final class PslIntegration implements IntegrationInterface
{
    /**
     * @var array<ContextualizerInterface>
     */
    private array $contexts;

    public function __construct()
    {
        $this->contexts = [
            new TypeTraceContextualizer(),
        ];
    }

    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(static function (Event $event, ?EventHint $hint): Event {
            $integration = SentrySdk::getCurrentHub()->getIntegration(self::class);

            if (null === $integration) {
                // this is bad?
                return $event;
            }

            if (null === $hint || null === $hint->exception) {
                // we are looking only for events with an exception
                return $event;
            }

            foreach ($integration->contexts as $context) {
                $context->addContext($event, $hint);
            }

            return $event;
        });
    }
}
