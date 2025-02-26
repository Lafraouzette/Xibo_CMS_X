<?php


namespace Xibo\XMR;

/**
 * Class LicenceCheckAction
 * @package Xibo\XMR
 */
class LicenceCheckAction extends PlayerAction
{
    public function __construct()
    {
        $this->setQos(4);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        $this->action = 'licenceCheck';

        return $this->serializeToJson();
    }
}
