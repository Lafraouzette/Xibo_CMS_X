<?php


namespace Xibo\Xmds\Listeners;

use Xibo\Event\XmdsDependencyRequestEvent;
use Xibo\Listener\ListenerConfigTrait;
use Xibo\Listener\ListenerLoggerTrait;
use Xibo\Support\Exception\NotFoundException;
use Xibo\Xmds\Entity\Dependency;

/**
 * Listener to handle dependency requests for data connectors.
 */
class XmdsDataConnectorListener
{
    use ListenerLoggerTrait;
    use ListenerConfigTrait;

    public function onDependencyRequest(XmdsDependencyRequestEvent $event)
    {
        // Can we return this type of file?
        if ($event->getFileType() === 'data_connector') {
            // Set the path
            $event->setRelativePathToLibrary('data_connectors/dataSet_' . $event->getRealId() . '.js');

            // No need to carry on, we've found it.
            $event->stopPropagation();
        }
    }

    /**
     * @throws NotFoundException
     */
    public static function getDataConnectorDependency(string $libraryLocation, int $dataSetId): Dependency
    {
        // Check that this asset is valid.
        $path = $libraryLocation
            . 'data_connectors' . DIRECTORY_SEPARATOR
            . 'dataSet_' . $dataSetId . '.js';

        if (!file_exists($path)) {
            throw new NotFoundException(sprintf(__('Data Connector %s not found'), $path));
        }

        // Return a dependency
        return new Dependency(
            'data_connector',
            $dataSetId,
            (Dependency::LEGACY_ID_OFFSET_DATA_CONNECTOR + $dataSetId) * -1,
            $path,
            filesize($path),
            file_get_contents($path . '.md5'),
            true
        );
    }
}
