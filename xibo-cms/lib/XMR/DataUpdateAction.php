<?php


namespace Xibo\XMR;

/**
 * Class DataUpdateAction
 *  Used to indicate that a widget has been recently updated and should be downloaded again
 * @package Xibo\XMR
 */
class DataUpdateAction extends PlayerAction
{
    /**
     * @param int $widgetId The widgetId which has been updated
     */
    public function __construct(protected int $widgetId)
    {
        $this->setQos(5);
    }

    /** @inheritdoc */
    public function getMessage(): string
    {
        $this->setQos(1);
        $this->action = 'dataUpdate';

        return $this->serializeToJson(['widgetId']);
    }
}
