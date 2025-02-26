<?php



namespace Xibo\XMR;

/**
 * Class ClearStatsAndLogsAction
 * @package Xibo\XMR
 */
class ClearStatsAndLogsAction extends PlayerAction
{
    public function __construct()
    {
        $this->setQos(2);
    }

    public function getMessage(): string
    {
        $this->action = 'clearStatsAndLogs';

        return $this->serializeToJson();
    }
}
