<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

final readonly class FoldersChanged
{
    /**
     * @param string[] $newFolders
     */
    public function __construct(public array $newFolders)
    {
    }
}
