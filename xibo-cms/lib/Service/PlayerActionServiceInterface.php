<?php

namespace Xibo\Service;

use Xibo\Entity\Display;
use Xibo\Support\Exception\GeneralException;
use Xibo\XMR\PlayerAction;

/**
 * Interface PlayerActionServiceInterface
 * @package Xibo\Service
 */
interface PlayerActionServiceInterface
{
    /**
     * PlayerActionHelper constructor.
     */
    public function __construct(ConfigServiceInterface $config, LogServiceInterface $log, bool $triggerPlayerActions);

    /**
     * @param Display[]|Display $displays
     * @param PlayerAction $action
     * @throws GeneralException
     */
    public function sendAction($displays, $action): void;

    /**
     * Get the queue
     */
    public function getQueue(): array;

    /**
     * Process the Queue of Actions
     * @throws GeneralException
     */
    public function processQueue(): void;
}
