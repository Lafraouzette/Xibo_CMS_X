<?php


namespace Xibo\Middleware;

use Xibo\Entity\Display;
use Xibo\Entity\DisplayProfile;
use Xibo\Service\ConfigServiceInterface;
use Xibo\Service\LogServiceInterface;
use Xibo\Support\Sanitizer\SanitizerInterface;

interface CustomDisplayProfileInterface
{
    /**
     * Return Display Profile type
     * @return string
     */
    public static function getType():string;

    /**
     * Return Display Profile name
     * @return string
     */
    public static function getName():string;

    /**
     * This function should return an array with default Display Profile config.
     *
     * @param ConfigServiceInterface $configService
     * @return array
     */
    public static function getDefaultConfig(ConfigServiceInterface $configService) : array;

    /**
     * This function should return full name, including extension (.twig) to the custom display profile edit form
     * the file is expected to be in the /custom folder along the custom Middleware.
     * To match naming convention twig file should be called displayprofile-form-edit-<type>.twig
     * This will be done automatically from the CustomDisplayProfileMiddlewareTrait.
     *
     * If you have named your twig file differently, override getCustomEditTemplate function in your middleware
     * @return string
     */
    public static function getCustomEditTemplate() : string;

    /**
     * This function handles any changes to the default Display Profile settings, as well as overrides per Display.
     * Each editable setting should have handling here.
     *
     * @param DisplayProfile $displayProfile
     * @param SanitizerInterface $sanitizedParams
     * @param array|null $config
     * @param Display|null $display
     * @param LogServiceInterface $logService
     * @return array
     */
    public static function editCustomConfigFields(DisplayProfile $displayProfile, SanitizerInterface $sanitizedParams, ?array $config, ?Display $display, LogServiceInterface $logService) : array;
}
