<?php



namespace Xibo\XMR;


class RekeyAction extends PlayerAction
{
    public function __construct()
    {
        $this->setQos(1);
    }

    public function getMessage(): string
    {
        $this->action = 'rekeyAction';

        return $this->serializeToJson();
    }
}
