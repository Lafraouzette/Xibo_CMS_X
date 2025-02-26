<?php


namespace Xibo\Widget\Compatibility;

use Xibo\Entity\Widget;
use Xibo\Widget\Provider\WidgetCompatibilityInterface;
use Xibo\Widget\Provider\WidgetCompatibilityTrait;

class NotificationViewCompatibility implements WidgetCompatibilityInterface
{
    use WidgetCompatibilityTrait;

    public function upgradeWidget(Widget $widget, int $fromSchema, int $toSchema): bool
    {
        $this->getLog()->debug('upgradeWidget: ' . $widget->getId() . ' from: ' . $fromSchema . ' to: ' . $toSchema);

        $upgraded = false;

        if ($fromSchema <= 1) {
            // Add a templateId.
            $widget->setOptionValue('templateId', 'attrib', 'message_custom_html');
            $upgraded = true;
        }

        return $upgraded;
    }

    public function saveTemplate(string $template, string $fileName): bool
    {
        return false;
    }
}
