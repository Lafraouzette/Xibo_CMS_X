<?php

namespace Xibo\Service;

use Stash\Interfaces\PoolInterface;
use Xibo\Storage\StorageServiceInterface;
use Xibo\Support\Exception\ConfigurationException;

/**
 * Interface ConfigServiceInterface
 * @package Xibo\Service
 */
interface ConfigServiceInterface
{
    /**
     * Set Service Dependencies
     * @param StorageServiceInterface $store
     * @param string $rootUri
     */
    public function setDependencies($store, $rootUri);

    /**
     * Get Cache Pool
     * @param PoolInterface $pool
     * @return mixed
     */
    public function setPool($pool);

    /**
     * Get Database Config
     * @return array
     */
    public function getDatabaseConfig();

    /**
     * Get settings
     * @return array|mixed|null
     */
    public function getSettings();

    /**
     * Gets the requested setting from the DB object given
     * @param $setting string
     * @param string[optional] $default
     * @param bool[optional] $full
     * @return string
     */
    public function getSetting($setting, $default = NULL, $full = false);

    /**
     * Change Setting
     * @param string $setting
     * @param mixed $value
     * @param int $userChange
     */
    public function changeSetting($setting, $value, $userChange = 0);

    /**
     * Is the provided setting visible
     * @param string $setting
     * @return bool
     */
    public function isSettingVisible($setting);

    /**
     * Is the provided setting editable
     * @param string $setting
     * @return bool
     */
    public function isSettingEditable($setting);

    /**
     * Should the host be considered a proxy exception
     * @param $host
     * @return bool
     */
    public function isProxyException($host);

    /**
     * Get Proxy Configuration
     * @param array $httpOptions
     * @return array
     */
    public function getGuzzleProxy($httpOptions = []);

    /**
     * Get API key details from Configuration
     * @return array
     */
    public function getApiKeyDetails();

    /**
     * Checks the Environment and Determines if it is suitable
     * @return string
     */
    public function checkEnvironment();

    /**
     * Loads the theme
     * @param string[Optional] $themeName
     * @throws ConfigurationException
     */
    public function loadTheme($themeName = null);

    /**
     * Get Theme Specific Settings
     * @param null $settingName
     * @param null $default
     * @return null
     */
    public function getThemeConfig($settingName = null, $default = null);

    /**
     * Get theme URI
     * @param string $uri
     * @param bool $local
     * @return string
     */
    public function uri($uri, $local = false);

    /**
     * Check a theme file exists
     * @param string $uri
     * @return bool
     */
    public function themeFileExists($uri);

    /**
     * Check a web file exists
     * @param string $uri
     * @return bool
     */
    public function fileExists($uri);

    /**
     * Get App Root URI
     * @return mixed
     */
    public function rootUri();

    /**
     * Get cache drivers
     * @return array
     */
    public function getCacheDrivers();

    /**
     * Get time series store settings
     * @return array
     */
    public function getTimeSeriesStore();

    /**
     * Get the cache namespace
     * @return string
     */
    public function getCacheNamespace();

    /**
     * Get Connector settings from the file based settings
     *  this acts as an override for settings stored in the database
     * @param string $connector The connector to return settings for.
     * @return array
     */
    public function getConnectorSettings(string $connector): array;
}
