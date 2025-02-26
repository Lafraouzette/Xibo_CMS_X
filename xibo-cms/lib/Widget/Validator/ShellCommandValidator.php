<?php


namespace Xibo\Widget\Validator;

use Xibo\Entity\Module;
use Xibo\Entity\Widget;
use Xibo\Support\Exception\InvalidArgumentException;
use Xibo\Widget\Provider\WidgetValidatorInterface;
use Xibo\Widget\Provider\WidgetValidatorTrait;

/**
 * Ensure a command has been entered somewhere in the widget
 */
class ShellCommandValidator implements WidgetValidatorInterface
{
    use WidgetValidatorTrait;

    /** @inheritDoc */
    public function validate(Module $module, Widget $widget, string $stage): void
    {
        if ($widget->getOptionValue('globalCommand', '') == ''
            && $widget->getOptionValue('androidCommand', '') == ''
            && $widget->getOptionValue('windowsCommand', '') == ''
            && $widget->getOptionValue('linuxCommand', '') == ''
            && $widget->getOptionValue('commandCode', '') == ''
            && $widget->getOptionValue('webosCommand', '') == ''
            && $widget->getOptionValue('tizenCommand', '') == ''
        ) {
            throw new InvalidArgumentException(__('You must enter a command'), 'command');
        }
    }
}
