<?php


namespace Xibo\XMR;

class CommandAction extends PlayerAction
{
    protected $commandCode;

    public function __construct()
    {
        $this->setQos(8);
    }

    /**
     * Set the command code
     * @param string $code
     * @return $this
     */
    public function setCommandCode($code)
    {
        $this->commandCode = $code;
        return $this;
    }

    public function getMessage(): string
    {
        $this->action = 'commandAction';

        if ($this->commandCode == '') {
            throw new PlayerActionException('Missing Command Code');
        }

        return $this->serializeToJson(['commandCode']);
    }
}
