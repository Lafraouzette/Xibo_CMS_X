<?php


namespace Xibo\Widget\Compatibility;

use Xibo\Entity\Widget;
use Xibo\Widget\Provider\WidgetCompatibilityInterface;
use Xibo\Widget\Provider\WidgetCompatibilityTrait;

/**
 * Convert a v3 calendar or calendaradvanced widget to its v4 counterpart.
 */
class CalendarWidgetCompatibility implements WidgetCompatibilityInterface
{
    use WidgetCompatibilityTrait;

    /** @inheritDoc */
    public function upgradeWidget(Widget $widget, int $fromSchema, int $toSchema): bool
    {
        $this->getLog()->debug('upgradeWidget: ' . $widget->getId() . ' from: ' . $fromSchema . ' to: ' . $toSchema);

        // Track if we've been upgraded.
        $upgraded = false;

        // Did we originally come from an agenda (the old calendar widget)
        if ($widget->getOriginalValue('type') === 'calendar') {
            $newTemplateId = 'event_custom_html';

            // New options names.
            $widget->changeOption('template', 'text');
        } else {
            // We are a calendaradvanced
            // Calendar type is either 1=schedule, 2=daily, 3=weekly or 4=monthly.
            $newTemplateId = match ($widget->getOptionValue('calendarType', 1)) {
                2 => 'daily',
                3 => 'weekly',
                4 => 'monthly',
                default => 'schedule',
            };

            // Apply the theme
            $newTemplateId .= '_' . $widget->getOptionValue('templateTheme', 'light');
        }

        if (!empty($newTemplateId)) {
            $widget->setOptionValue('templateId', 'attrib', $newTemplateId);
            $upgraded = true;
        }

        return $upgraded;
    }

    public function saveTemplate(string $template, string $fileName): bool
    {
        return false;
    }
}
