<?php


namespace Xibo\Widget\Validator;

use Illuminate\Support\Str;
use Xibo\Entity\Module;
use Xibo\Entity\Widget;
use Xibo\Support\Exception\InvalidArgumentException;
use Xibo\Widget\Provider\WidgetValidatorInterface;
use Xibo\Widget\Provider\WidgetValidatorTrait;

/**
 * Validate that we have a duration greater than 0
 */
class RemoteUrlsZeroDurationValidator implements WidgetValidatorInterface
{
    use WidgetValidatorTrait;

    /**
     * @inheritDoc
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     */
    public function validate(Module $module, Widget $widget, string $stage): void
    {
        $url = urldecode($widget->getOptionValue('uri', ''));
        if ($widget->useDuration === 1
            && $widget->duration <= 0
            && !Str::startsWith($url, 'file://')
            && Str::contains($url, '://')
        ) {
            // This is not a locally stored file, and so we should have a duration
            throw new InvalidArgumentException(
                __('The duration needs to be greater than 0 for remote URLs'),
                'duration'
            );
        } else if ($widget->useDuration === 1 && $widget->duration <= 0) {
            // Locally stored file, still needs a positive duration.
            throw new InvalidArgumentException(
                __('The duration needs to be above 0 for a locally stored file '),
                'duration'
            );
        }
    }
}
