<?php


namespace Xibo\Connector;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Stash\Interfaces\PoolInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Xibo\Service\JwtServiceInterface;
use Xibo\Service\PlayerActionServiceInterface;
use Xibo\Support\Sanitizer\SanitizerInterface;

/**
 * Connector Interface
 */
interface ConnectorInterface
{
    public function setFactories(ContainerInterface $container): ConnectorInterface;
    public function registerWithDispatcher(EventDispatcherInterface $dispatcher): ConnectorInterface;
    public function useLogger(LoggerInterface $logger): ConnectorInterface;
    public function useSettings(array $settings, bool $isProvider = true): ConnectorInterface;
    public function usePool(PoolInterface $pool): ConnectorInterface;
    public function useHttpOptions(array $httpOptions): ConnectorInterface;
    public function useJwtService(JwtServiceInterface $jwtService): ConnectorInterface;
    public function usePlayerActionService(PlayerActionServiceInterface $playerActionService): ConnectorInterface;
    public function getClient(): Client;
    public function getSourceName(): string;
    public function getTitle(): string;
    public function getDescription(): string;
    public function getThumbnail(): string;
    public function getSetting($setting, $default = null);
    public function isProviderSetting($setting): bool;
    public function getSettingsFormTwig(): string;
    public function getSettingsFormJavaScript(): string;
    public function processSettingsForm(SanitizerInterface $params, array $settings): array;
}
