<?php

namespace Xibo\Tests\Helper;

use Xibo\Service\ConfigServiceInterface;
use Xibo\Service\PlayerActionServiceInterface;

/**
 * Class MockPlayerActionService
 * @package Helper
 */
class MockPlayerActionService implements PlayerActionServiceInterface
{
    /** @var \Xibo\Service\LogServiceInterface */
    private $log;

    private $displays = [];

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
        $this->log->debug('MockPlayerActionService: sendAction');

        if (!is_array($displays)) {
            $displays = [$displays];
        }

        foreach ($displays as $display) {
            $this->displays[] = $display->displayId;
        }
    }

    /**
     * @inheritdoc
     */
    public function getQueue(): array
    {
        $this->log->debug('MockPlayerActionService: getQueue');
        return $this->displays;
    }

    /**
     * @inheritdoc
     */
    public function processQueue(): void
    {
        $this->log->debug('MockPlayerActionService: processQueue');
    }
}