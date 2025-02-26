<?php


namespace Xibo\Xmds\Listeners;

use Xibo\Event\XmdsDependencyListEvent;
use Xibo\Event\XmdsDependencyRequestEvent;
use Xibo\Listener\ListenerCacheTrait;
use Xibo\Listener\ListenerConfigTrait;
use Xibo\Listener\ListenerLoggerTrait;
use Xibo\Support\Exception\GeneralException;

/**
 * XMDS player bundle listener
 *  responsible for adding the player bundle to the list of required files, and for returning the player bundle
 *  when requested.
 */
class XmdsPlayerBundleListener
{
    use ListenerLoggerTrait;
    use ListenerConfigTrait;

    public function onDependencyList(XmdsDependencyListEvent $event)
    {
        $this->getLogger()->debug('onDependencyList: XmdsPlayerBundleListener');

        // Output the player bundle
        $forceUpdate = false;
        $bundlePath = $this->getConfig()->getSetting('LIBRARY_LOCATION') . 'assets/bundle.min.js';
        if (!file_exists($bundlePath)) {
            $result = @copy(PROJECT_ROOT . '/modules/bundle.min.js', $bundlePath);
            if (!$result) {
                throw new GeneralException('Unable to copy asset');
            }
            $forceUpdate = true;
        }

        // Get the bundle MD5
        $bundleMd5CachePath = $bundlePath . '.md5';
        if (!file_exists($bundleMd5CachePath) || $forceUpdate) {
            $bundleMd5 = md5_file($bundlePath);
            file_put_contents($bundleMd5CachePath, $bundleMd5);
        } else {
            $bundleMd5 = file_get_contents($bundlePath . '.md5');
        }

        $event->addDependency(
            'bundle',
            1,
            'assets/bundle.min.js',
            filesize($bundlePath),
            $bundleMd5,
            true,
            -1
        );
    }

    public function onDependencyRequest(XmdsDependencyRequestEvent $event)
    {
        // Can we return this type of file?
        if ($event->getFileType() === 'bundle' && $event->getRealId() == 1) {
            // Set the path
            $event->setRelativePathToLibrary('assets/bundle.min.js');

            // No need to carry on, we've found it.
            $event->stopPropagation();
        }
    }
}
