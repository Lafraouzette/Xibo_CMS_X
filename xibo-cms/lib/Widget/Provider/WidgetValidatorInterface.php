<?php


namespace Xibo\Widget\Provider;

use Psr\Log\LoggerInterface;
use Xibo\Entity\Module;
use Xibo\Entity\Widget;

/**
 * Widget Validator Interface
 * --------------------------
 * Used to validate the properties of a module after it all of its individual properties and those of its
 * template have been validated via their property rules.
 */
interface WidgetValidatorInterface
{
    public function getLog(): LoggerInterface;

    public function setLog(LoggerInterface $logger): WidgetValidatorInterface;

    /**
     * Validate the widget provided
     * @param Module $module The Module
     * @param Widget $widget The Widget - this is read only
     * @param string $stage Which stage are we validating, either `save` or `status`
     */
    public function validate(Module $module, Widget $widget, string $stage): void;
}
