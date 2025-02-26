<?php


namespace Xibo\Xmds\Listeners;

use Xibo\Event\XmdsDependencyRequestEvent;
use Xibo\Factory\ModuleFactory;
use Xibo\Factory\ModuleTemplateFactory;
use Xibo\Listener\ListenerConfigTrait;
use Xibo\Listener\ListenerLoggerTrait;
use Xibo\Support\Exception\NotFoundException;

/**
 * Listener which handles requests for assets.
 */
class XmdsAssetsListener
{
    use ListenerLoggerTrait;
    use ListenerConfigTrait;

    /** @var \Xibo\Factory\ModuleFactory */
    private $moduleFactory;

    /** @var \Xibo\Factory\ModuleTemplateFactory */
    private $moduleTemplateFactory;

    /**
     * @param \Xibo\Factory\ModuleFactory $moduleFactory
     * @param \Xibo\Factory\ModuleTemplateFactory $moduleTemplateFactory
     */
    public function __construct(ModuleFactory $moduleFactory, ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleFactory = $moduleFactory;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function onDependencyRequest(XmdsDependencyRequestEvent $event): void
    {
        $this->getLogger()->debug('onDependencyRequest: XmdsAssetsListener');

        if ($event->getFileType() === 'asset') {
            // Get the asset using only the assetId.
            try {
                $asset = $this->moduleFactory
                    ->getAssetsFromAnywhereById($event->getRealId(), $this->moduleTemplateFactory);

                if ($asset->isSendToPlayer()) {
                    // Make sure the asset cache is there
                    $asset->updateAssetCache($this->getConfig()->getSetting('LIBRARY_LOCATION'));

                    // Return the full path to this asset
                    $event->setRelativePathToLibrary('assets/' . $asset->getFilename());
                    $event->stopPropagation();
                } else {
                    $this->getLogger()->debug('onDependencyRequest: asset found but is cms only');
                }
            } catch (NotFoundException $notFoundException) {
                $this->getLogger()->info('onDependencyRequest: No asset found for assetId: '
                    . $event->getRealId());
            }
        }
    }
}
