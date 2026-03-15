<?php
declare(strict_types=1);

namespace Nexendrie\Translation\Events;

final class FoldersChanged
{
    /**
     * @param string[] $newFolders
     */
    public function __construct(public readonly array $newFolders)
    {
    }
}
