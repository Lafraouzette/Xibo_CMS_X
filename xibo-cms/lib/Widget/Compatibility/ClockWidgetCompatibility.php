<?php


namespace Xibo\Widget\Compatibility;

use Xibo\Entity\Widget;
use Xibo\Widget\Provider\WidgetCompatibilityInterface;
use Xibo\Widget\Provider\WidgetCompatibilityTrait;

/**
 * Convert widget from an old schema to a new schema
 */
class ClockWidgetCompatibility implements WidgetCompatibilityInterface
{
    use WidgetCompatibilityTrait;

    /** @inheritdoc
     */
    public function upgradeWidget(Widget $widget, int $fromSchema, int $toSchema): bool
    {
        $this->getLog()->debug('upgradeWidget: ' . $widget->getId() . ' from: ' . $fromSchema . ' to: ' . $toSchema);

        // The old clock widget had a `clockTypeId` option which determines the template
        // we must make a choice here.
        $widget->type = match ($widget->getOptionValue('clockTypeId', 1)) {
            2 => 'clock-digital',
            3 => 'clock-flip',
            default => 'clock-analogue',
        };

        // in v3 this option used to ba called theme, now it is themeId
        if ($widget->type === 'clock-analogue') {
            $widget->setOptionValue('themeId', 'attrib', $widget->getOptionValue('theme', 1));
        }

        // We don't need the old options anymore
        $widget->removeOption('clockTypeId');
        $widget->removeOption('theme');

        return true;
    }

    public function saveTemplate(string $template, string $fileName): bool
    {
        return false;
    }
}
