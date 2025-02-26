<?php



namespace Xibo\XMR;

/**
 * Class CollectNowAction
 * @package Xibo\XMR
 */
class CollectNowAction extends PlayerAction
{
    public function __construct()
    {
        $this->setQos(5);
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        $this->action = 'collectNow';

        return $this->serializeToJson();
    }
}
