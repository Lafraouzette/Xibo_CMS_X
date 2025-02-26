<?php


namespace Xibo\Widget\Validator;

use Xibo\Entity\Module;
use Xibo\Entity\Widget;
use Xibo\Support\Exception\InvalidArgumentException;
use Xibo\Widget\Provider\WidgetValidatorInterface;
use Xibo\Widget\Provider\WidgetValidatorTrait;

/**
 * Validate that we have a duration greater than 0
 */
class ZeroDurationValidator implements WidgetValidatorInterface
{
    use WidgetValidatorTrait;

    /**
     * @inheritDoc
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     */
    public function validate(Module $module, Widget $widget, string $stage): void
    {
        // Videos can have 0 durations (but not if useDuration is selected)
        if ($widget->useDuration === 1 && $widget->duration <= 0) {
            throw new InvalidArgumentException(
                sprintf(__('Duration needs to be above 0 for %s'), $module->name),
                'duration'
            );
        }
    }
}
