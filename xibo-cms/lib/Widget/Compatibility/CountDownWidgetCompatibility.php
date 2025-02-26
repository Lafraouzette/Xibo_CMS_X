<?php


namespace Xibo\Widget\Compatibility;

use Xibo\Entity\Widget;
use Xibo\Widget\Provider\WidgetCompatibilityInterface;
use Xibo\Widget\Provider\WidgetCompatibilityTrait;

/**
 * Convert widget from an old schema to a new schema
 */
class CountDownWidgetCompatibility implements WidgetCompatibilityInterface
{
    use WidgetCompatibilityTrait;

    /** @inheritdoc
     */
    public function upgradeWidget(Widget $widget, int $fromSchema, int $toSchema): bool
    {
        $this->getLog()->debug('upgradeWidget: ' . $widget->getId() . ' from: ' . $fromSchema . ' to: ' . $toSchema);

        $countdownType = $widget->getOptionValue('countdownType', 1);
        $overrideTemplate = $widget->getOptionValue('overrideTemplate', 0);

        // Old countdown had countdownType.
        if ($overrideTemplate == 1) {
            $widget->type = 'countdown-custom';
        } else {
            $widget->type = match ($countdownType) {
                2 => 'countdown-clock',
                3 => 'countdown-table',
                4 => 'countdown-days',
                default => 'countdown-text',
            };
        }

        // If overriden, we need to tranlate the legacy options to the new values
        if ($overrideTemplate == 1) {
            $widget->changeOption('widgetOriginalWidth', 'widgetDesignWidth');
            $widget->changeOption('widgetOriginalHeight', 'widgetDesignHeight');
            $widget->removeOption('templateId');
        }

        return true;
    }

    public function saveTemplate(string $template, string $fileName): bool
    {
        return false;
    }
}
