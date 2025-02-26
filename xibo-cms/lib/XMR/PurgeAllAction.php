<?php


namespace Xibo\XMR;

/**
 * Class PurgeAllAction
 * @package Xibo\XMR
 */
class PurgeAllAction extends PlayerAction
{
    public function __construct()
    {
        $this->setQos(2);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        $this->action = 'purgeAll';

        return $this->serializeToJson();
    }
}
