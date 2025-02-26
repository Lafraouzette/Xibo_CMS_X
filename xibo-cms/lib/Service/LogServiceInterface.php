<?php



namespace Xibo\Service;

use Psr\Log\LoggerInterface;

/**
 * Interface LogServiceInterface
 * @package Xibo\Service
 */
interface LogServiceInterface
{
    /**
     * Log constructor.
     * @param LoggerInterface $logger
     * @param string $mode
     */
    public function __construct($logger, $mode = 'production');

    /**
     * Get the underlying logger interface
     *  useful for custom code and modules which may not want to know about the full Xibo LogServiceInterface
     * @return \Psr\Log\LoggerInterface
     */
    public function getLoggerInterface(): LoggerInterface;

    public function getUserId(): ?int;
    public function getSessionHistoryId(): ?int;
    public function getRequestId(): ?int;

    /**
     * Set the user Id
     * @param int $userId
     */
    public function setUserId($userId);

    /**
     * Set the User IP Address
     * @param $ip
     * @return mixed
     */
    public function setIpAddress($ip);

    /**
     * Set history session id
     * @param $sessionHistoryId
     * @return mixed
     */
    public function setSessionHistoryId($sessionHistoryId);

    /**
     * Set API requestId
     * @param $requestId
     * @return mixed
     */
    public function setRequestId($requestId);

    /**
     * @param $mode
     * @return mixed
     */
    public function setMode($mode);

    /**
     * Audit Log
     * @param string $entity
     * @param int $entityId
     * @param string $message
     * @param string|object|array $object
     */
    public function audit($entity, $entityId, $message, $object);

    /**
     * @param $sql
     * @param $params
     * @param bool $logAsError
     * @return mixed
     */
    public function sql($sql, $params, $logAsError = false);

    /**
     * @param string
     * @return mixed
     */
    public function debug($object);

    /**
     * @param ...$object
     * @return mixed
     */
    public function notice($object);

    /**
     * @param ...$object
     * @return mixed
     */
    public function info($object);

    /**
     * @param ...$object
     * @return mixed
     */
    public function warning($object);

    /**
     * @param ...$object
     * @return mixed
     */
    public function error($object);

    /**
     * @param ...$object
     * @return mixed
     */
    public function critical($object);

    /**
     * @param ...$object
     * @return mixed
     */
    public function alert($object);

    /**
     * @param ...$object
     * @return mixed
     */
    public function emergency($object);

    /**
     * Resolve the log level
     * @param string $level
     * @return int
     */
    public static function resolveLogLevel($level);

    /**
     * Set the log level on all handlers
     * @param $level
     */
    public function setLevel($level);
}
