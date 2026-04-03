<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

final readonly class UntranslatedMessage
{
    public function __construct(public string $message)
    {
    }
}
