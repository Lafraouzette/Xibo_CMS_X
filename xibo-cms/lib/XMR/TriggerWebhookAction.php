<?php


namespace Xibo\XMR;

/**
 * Trigger a web hook on the player. This is intended mainly for testing purposes as you'd
 * expect the trigger to be sent locally on the player
 */
class TriggerWebhookAction extends PlayerAction
{
    public $triggerCode;

    public function __construct($triggerCode)
    {
        $this->triggerCode = $triggerCode;
        $this->setQos(3);
    }

    /**
     * @return string
     * @throws PlayerActionException
     */
    public function getMessage(): string
    {
        $this->action = 'triggerWebhook';

        if ($this->triggerCode == '') {
            throw new PlayerActionException('Layout Details not provided');
        }

        return $this->serializeToJson(['triggerCode']);
    }
}
