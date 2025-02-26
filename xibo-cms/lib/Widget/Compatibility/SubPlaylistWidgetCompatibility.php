<?php


namespace Xibo\Widget\Compatibility;

use Xibo\Entity\Widget;
use Xibo\Widget\Provider\WidgetCompatibilityInterface;
use Xibo\Widget\Provider\WidgetCompatibilityTrait;
use Xibo\Widget\SubPlaylistItem;

/**
 * Convert widget from an old schema to a new schema
 */
class SubPlaylistWidgetCompatibility implements WidgetCompatibilityInterface
{
    use WidgetCompatibilityTrait;

    /** @inheritdoc
     */
    public function upgradeWidget(Widget $widget, int $fromSchema, int $toSchema): bool
    {
        $this->getLog()->debug('upgradeWidget: ' . $widget->getId() . ' from: ' . $fromSchema . ' to: ' . $toSchema);

        $upgraded = false;
        $playlists = [];
        $playlistIds = [];

        // subPlaylistOptions and subPlaylistIds are no longer in use from 2.3
        // we need to capture these options to support Layout with sub-playlist import from older CMS
        foreach ($widget->widgetOptions as $option) {
            if ($option->option === 'subPlaylists') {
                $playlists = json_decode($widget->getOptionValue('subPlaylists', '[]'), true);
            }

            if ($option->option === 'subPlaylistIds') {
                $playlistIds = json_decode($widget->getOptionValue('subPlaylistIds', '[]'), true);
            }

            if ($option->option === 'subPlaylistOptions') {
                $subPlaylistOptions = json_decode($widget->getOptionValue('subPlaylistOptions', '[]'), true);
            }
        }

        if (count($playlists) <= 0) {
            $i = 0;
            foreach ($playlistIds as $playlistId) {
                $i++;
                $playlists[] = [
                    'rowNo' => $i,
                    'playlistId' => $playlistId,
                    'spotFill' => $subPlaylistOptions[$playlistId]['subPlaylistIdSpotFill'] ?? null,
                    'spotLength' => $subPlaylistOptions[$playlistId]['subPlaylistIdSpotLength'] ?? null,
                    'spots' => $subPlaylistOptions[$playlistId]['subPlaylistIdSpots'] ?? null,
                ];
            }

            $playlistItems = [];
            foreach ($playlists as $playlist) {
                $item = new SubPlaylistItem();
                $item->rowNo = intval($playlist['rowNo']);
                $item->playlistId = $playlist['playlistId'];
                $item->spotFill = $playlist['spotFill'] ?? '';
                $item->spotLength =  $playlist['spotLength'] !== '' ? intval($playlist['spotLength']) : '';
                $item->spots = $playlist['spots'] !== '' ? intval($playlist['spots']) : '';

                $playlistItems[] = $item;
            }

            if (count($playlistItems) > 0) {
                $widget->setOptionValue('subPlaylists', 'attrib', json_encode($playlistItems));
                $widget->removeOption('subPlaylistIds');
                $widget->removeOption('subPlaylistOptions');
                $upgraded = true;
            }
        } else {
            $this->getLog()->debug(
                'upgradeWidget : subplaylist ' . $widget->widgetId .
                ' with already updated widget options, save to update schema version'
            );
            $upgraded = true;
        }

        return $upgraded;
    }

    public function saveTemplate(string $template, string $fileName): bool
    {
        return false;
    }
}
