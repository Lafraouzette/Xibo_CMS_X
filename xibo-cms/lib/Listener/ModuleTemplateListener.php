<?php


namespace Xibo\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Xibo\Event\ParsePermissionEntityEvent;
use Xibo\Factory\ModuleTemplateFactory;

class ModuleTemplateListener
{
    use ListenerLoggerTrait;

    /** @var ModuleTemplateFactory  */
    private ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function registerWithDispatcher(EventDispatcherInterface $dispatcher): ModuleTemplateListener
    {
        $dispatcher->addListener(ParsePermissionEntityEvent::$NAME . 'moduleTemplate', [$this, 'onParsePermissions']);

        return $this;
    }

    /**
     * @param ParsePermissionEntityEvent $event
     * @return void
     * @throws \Xibo\Support\Exception\NotFoundException
     */
    public function onParsePermissions(ParsePermissionEntityEvent $event)
    {
        $this->getLogger()->debug('onParsePermissions');
        $event->setObject($this->moduleTemplateFactory->getUserTemplateById($event->getObjectId()));
    }
}
