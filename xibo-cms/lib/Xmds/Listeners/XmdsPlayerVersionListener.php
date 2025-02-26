<?php


namespace Xibo\Xmds\Listeners;

use Xibo\Event\XmdsDependencyListEvent;
use Xibo\Event\XmdsDependencyRequestEvent;
use Xibo\Factory\PlayerVersionFactory;
use Xibo\Listener\ListenerLoggerTrait;
use Xibo\Support\Exception\NotFoundException;
use Xibo\Xmds\Entity\Dependency;

/**
 * A listener to supply player version files to players
 */
class XmdsPlayerVersionListener
{
    use ListenerLoggerTrait;

    use XmdsListenerTrait;

    /**
     * @var PlayerVersionFactory
     */
    private $playerVersionFactory;

    public function __construct(PlayerVersionFactory $playerVersionFactory)
    {
        $this->playerVersionFactory = $playerVersionFactory;
    }

    public function onDependencyList(XmdsDependencyListEvent $event): void
    {
        $this->getLogger()->debug('onDependencyList: XmdsPlayerVersionListener');

        // We do not supply a dependency to SSSP players.
        if ($event->getDisplay()->clientType === 'sssp') {
            return;
        }

        try {
            $playerVersionMediaId = $event->getDisplay()
                ->getSetting('versionMediaId', null, ['displayOverride' => true]);

            // If it isn't set, then we have nothing to do.
            if (empty($playerVersionMediaId)) {
                return;
            }

            $version = $this->playerVersionFactory->getById($playerVersionMediaId);
            $event->addDependency(
                'playersoftware',
                $version->versionId,
                'playersoftware/' . $version->fileName,
                $version->size,
                $version->md5,
                true,
                $this->getLegacyId($playerVersionMediaId, Dependency::LEGACY_ID_OFFSET_PLAYER_SOFTWARE)
            );
        } catch (NotFoundException $notFoundException) {
            // Ignore this
            $this->getLogger()->error('onDependencyList: player version not found for displayId '
                . $event->getDisplay()->displayId);
        }
    }

    public function onDependencyRequest(XmdsDependencyRequestEvent $event)
    {
        $this->getLogger()->debug('onDependencyRequest: XmdsPlayerVersionListener');

        if ($event->getFileType() === 'playersoftware') {
            $version = $this->playerVersionFactory->getById($event->getRealId());
            $event->setRelativePathToLibrary('playersoftware/' . $version->fileName);
        }
    }
}
