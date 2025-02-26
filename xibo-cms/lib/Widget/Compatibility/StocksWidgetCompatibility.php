<?php


namespace Xibo\Widget\Compatibility;

use Xibo\Entity\Widget;
use Xibo\Widget\Provider\WidgetCompatibilityInterface;
use Xibo\Widget\Provider\WidgetCompatibilityTrait;

/**
 * Convert widget from an old schema to a new schema
 */
class StocksWidgetCompatibility implements WidgetCompatibilityInterface
{
    use WidgetCompatibilityTrait;

    /** @inheritdoc
     */
    public function upgradeWidget(Widget $widget, int $fromSchema, int $toSchema): bool
    {
        $this->getLog()->debug('upgradeWidget: ' . $widget->getId() . ' from: ' . $fromSchema . ' to: ' . $toSchema);

        $upgraded = false;
        $overrideTemplate = $widget->getOptionValue('overrideTemplate', 0);

        // If the widget has override template
        if ($overrideTemplate == 1) {
            $widget->setOptionValue('templateId', 'attrib', 'stocks_custom_html');
            $upgraded = true;

            // We need to tranlate the legacy options to the new values
            $widget->changeOption('widgetOriginalWidth', 'widgetDesignWidth');
            $widget->changeOption('widgetOriginalHeight', 'widgetDesignHeight');
        }

        // We need to change duration per page to duration per item
        $widget->changeOption('durationIsPerPage', 'durationIsPerItem');

        return $upgraded;
    }

    public function saveTemplate(string $template, string $fileName): bool
    {
        return false;
    }
}

