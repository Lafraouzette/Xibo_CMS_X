<?php


namespace Xibo\XMR;

class RevertToSchedule extends PlayerAction
{
    public function __construct()
    {
        $this->setQos(10);
    }

    public function getMessage(): string
    {
        $this->action = 'revertToSchedule';

        return $this->serializeToJson();
    }
}
