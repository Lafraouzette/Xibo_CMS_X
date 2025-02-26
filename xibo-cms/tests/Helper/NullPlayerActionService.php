<?php

namespace Xibo\Tests\Helper;

use Xibo\Service\ConfigServiceInterface;
use Xibo\Service\PlayerActionServiceInterface;

/**
 * Class NullPlayerActionService
 * @package Helper
 */
class NullPlayerActionService implements PlayerActionServiceInterface
{
    /** @var \Xibo\Service\LogServiceInterface */
    private $log;

    /**
     * @inheritdoc
     */
    public function __construct(ConfigServiceInterface $config, $log, $triggerPlayerActions)
    {
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function sendAction($displays, $action): void
    {
        $this->log->debug('NullPlayerActionService: sendAction');
    }

    /**
     * @inheritdoc
     */
    public function getQueue(): array
    {
        $this->log->debug('NullPlayerActionService: getQueue');
        return [];
    }

    /**
     * @inheritdoc
     */
    public function processQueue(): void
    {
        $this->log->debug('NullPlayerActionService: processQueue');
    }
}
