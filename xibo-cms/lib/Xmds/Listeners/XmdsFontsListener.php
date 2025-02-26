<?php


namespace Xibo\Xmds\Listeners;

use Xibo\Event\XmdsDependencyListEvent;
use Xibo\Event\XmdsDependencyRequestEvent;
use Xibo\Factory\FontFactory;
use Xibo\Listener\ListenerConfigTrait;
use Xibo\Listener\ListenerLoggerTrait;
use Xibo\Xmds\Entity\Dependency;

/**
 * A listener to supply fonts as dependencies to players.
 */
class XmdsFontsListener
{
    use ListenerLoggerTrait;
    use ListenerConfigTrait;

    use XmdsListenerTrait;

    /**
     * @var FontFactory
     */
    private $fontFactory;

    public function __construct(FontFactory $fontFactory)
    {
        $this->fontFactory = $fontFactory;
    }

    public function onDependencyList(XmdsDependencyListEvent $event): void
    {
        $this->getLogger()->debug('onDependencyList: XmdsFontsListener');

        foreach ($this->fontFactory->query() as $font) {
            $event->addDependency(
                'font',
                $font->id,
                'fonts/' . $font->fileName,
                $font->size,
                $font->md5,
                true,
                $this->getLegacyId($font->id, Dependency::LEGACY_ID_OFFSET_FONT)
            );
        }

        // Always add fonts.css to the list.
        // This can have the ID of 1 because it is a different file type and will therefore be unique.
        $fontsCssPath = $this->getConfig()->getSetting('LIBRARY_LOCATION') . 'fonts/fonts.css';
        $event->addDependency(
            'fontCss',
            1,
            'fonts/fonts.css',
            filesize($fontsCssPath),
            md5_file($fontsCssPath),
            true,
            $this->getLegacyId(0, Dependency::LEGACY_ID_OFFSET_FONT)
        );
    }

    public function onDependencyRequest(XmdsDependencyRequestEvent $event): void
    {
        $this->getLogger()->debug('onDependencyRequest: XmdsFontsListener');

        if ($event->getFileType() === 'font') {
            $font = $this->fontFactory->getById($event->getRealId());
            $event->setRelativePathToLibrary('fonts/' . $font->fileName);
        } else if ($event->getFileType() === 'fontCss') {
            $event->setRelativePathToLibrary('fonts/fonts.css');
        }
    }
}
