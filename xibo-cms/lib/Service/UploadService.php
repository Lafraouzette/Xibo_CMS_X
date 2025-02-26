<?php


namespace Xibo\Service;

use Xibo\Helper\ApplicationState;
use Xibo\Helper\UploadHandler;

/**
 * Upload Service to scaffold an upload handler
 */
class UploadService
{
    /**
     * UploadService constructor.
     * @param string $uploadDir
     * @param array $settings
     * @param LogServiceInterface $logger
     * @param ApplicationState $state
     */
    public function __construct(
        private readonly string $uploadDir,
        private readonly array $settings,
        private readonly LogServiceInterface $logger,
        private readonly ApplicationState $state
    ) {
    }

    /**
     * Create a new upload handler
     * @return UploadHandler
     */
    public function createUploadHandler(): UploadHandler
    {
        // Blue imp requires an extra /
        $handler = new UploadHandler($this->uploadDir, $this->logger->getLoggerInterface(), $this->settings, false);

        return $handler->setState($this->state);
    }
}
