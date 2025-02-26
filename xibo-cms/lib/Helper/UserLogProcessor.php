<?php



namespace Xibo\Helper;

/**
 * Class UserLogProcessor
 * @package Xibo\Helper
 */
class UserLogProcessor
{
    /**
     * UserLogProcessor
     * @param int $userId
     * @param int|null $sessionHistoryId
     * @param int|null $requestId
     */
    public function __construct(
        private readonly int $userId,
        private readonly ?int $sessionHistoryId,
        private readonly ?int $requestId
    ) {
    }

    /**
     * @param array $record
     * @return array
     */
    public function __invoke(array $record): array
    {
        $record['extra']['userId'] = $this->userId;
        
        if ($this->sessionHistoryId != null) {
            $record['extra']['sessionHistoryId'] = $this->sessionHistoryId;
        }

        if ($this->requestId != null) {
            $record['extra']['requestId'] = $this->requestId;
        }

        return $record;
    }
}
