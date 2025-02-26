<?php



namespace Xibo\Helper;

/**
 * Class RouteLogProcessor
 *  a process to add route/method information to the log record
 * @package Xibo\Helper
 */
class RouteLogProcessor
{
    /**
     * Log Processor
     * @param string $route
     * @param string $method
     */
    public function __construct(
        private readonly string $route,
        private readonly string $method
    ) {
    }

    /**
     * @param array $record
     * @return array
     */
    public function __invoke(array $record): array
    {
        $record['extra']['method'] = $this->method;
        $record['extra']['route'] = $this->route;
        return $record;
    }
}
