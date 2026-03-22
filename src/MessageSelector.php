<?php
declare(strict_types=1);

namespace Nexendrie\Translation;

if (false) { // @phpstan-ignore if.alwaysFalse
    /**
     * @deprecated Use IntervalsMessageSelector
     */
    final class MessageSelector implements IMessageSelector
    {
        public function isMultiChoice(string $message): bool
        {
            return false;
        }

        public function choose(string $message, int $count): string
        {
            return $message;
        }

    }
} else {
    class_alias(IntervalsMessageSelector::class, MessageSelector::class);
}
