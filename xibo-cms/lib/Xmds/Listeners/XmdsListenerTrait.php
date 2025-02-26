<?php


namespace Xibo\Xmds\Listeners;

/**
 * A trait added to all Xmds Listeners
 */
trait XmdsListenerTrait
{
    /**
     * Get a Legacy ID we can use for older XMDS schema versions
     * @param int $id
     * @param int $offset
     * @return int
     */
    private function getLegacyId(int $id, int $offset): int
    {
        return ($id + $offset) * -1;
    }
}
