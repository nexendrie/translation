<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

final class UntranslatedMessage
{
    public function __construct(public readonly string $message)
    {
    }
}
