<?php


namespace Xibo\Widget\Compatibility;

use Xibo\Entity\Widget;
use Xibo\Widget\Provider\WidgetCompatibilityInterface;
use Xibo\Widget\Provider\WidgetCompatibilityTrait;

/**
 * Convert widget from an old schema to a new schema
 */
class SocialMediaWidgetCompatibility implements WidgetCompatibilityInterface
{
    use WidgetCompatibilityTrait;

    /** @inheritdoc
     */
    public function upgradeWidget(Widget $widget, int $fromSchema, int $toSchema): bool
    {
        $this->getLog()->debug('upgradeWidget: ' . $widget->getId() . ' from: ' . $fromSchema . ' to: ' . $toSchema);

        $overrideTemplate = $widget->getOptionValue('overrideTemplate', 0);
        if ($overrideTemplate == 1) {
            $newTemplateId = 'social_media_custom_html';
        } else {
            $newTemplateId = match ($widget->getOptionValue('templateId', '')) {
                'full-timeline-np' => 'social_media_static_1',
                'full-timeline' => 'social_media_static_2',
                'tweet-with-profileimage-left' => 'social_media_static_4',
                'tweet-with-profileimage-right' => 'social_media_static_5',
                'tweet-1' => 'social_media_static_6',
                'tweet-2' => 'social_media_static_7',
                'tweet-4' => 'social_media_static_8',
                'tweet-6NP' => 'social_media_static_9',
                'tweet-6PL' => 'social_media_static_10',
                'tweet-7' => 'social_media_static_11',
                'tweet-8' => 'social_media_static_12',
                default => 'social_media_static_3',
            };
        }
        $widget->setOptionValue('templateId', 'attrib', $newTemplateId);

        // If overriden, we need to tranlate the legacy options to the new values
        if ($overrideTemplate == 1) {
            $widget->changeOption('widgetOriginalWidth', 'widgetDesignWidth');
            $widget->changeOption('widgetOriginalHeight', 'widgetDesignHeight');
            $widget->changeOption('widgetOriginalPadding', 'widgetDesignGap');
        }

        return true;
    }

    public function saveTemplate(string $template, string $fileName): bool
    {
        return false;
    }
}
