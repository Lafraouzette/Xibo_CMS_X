<?php


namespace Xibo\XMR;

class ScreenShotAction extends PlayerAction
{
    public function __construct()
    {
        $this->setQos(5);
    }

    public function getMessage(): string
    {
        $this->action = 'screenShot';

        return $this->serializeToJson();
    }
}
