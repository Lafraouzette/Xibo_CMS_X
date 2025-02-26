<?php


namespace Xibo\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Xibo\Event\FolderMovingEvent;
use Xibo\Event\ParsePermissionEntityEvent;
use Xibo\Event\UserDeleteEvent;
use Xibo\Factory\SyncGroupFactory;
use Xibo\Storage\StorageServiceInterface;

/**
 * SyncGroup events
 */
class SyncGroupListener
{
    use ListenerLoggerTrait;
    private SyncGroupFactory $syncGroupFactory;
    private StorageServiceInterface $storageService;

    /**
     * @param SyncGroupFactory $syncGroupFactory
     * @param StorageServiceInterface $storageService
     */
    public function __construct(
        SyncGroupFactory $syncGroupFactory,
        StorageServiceInterface $storageService
    ) {
        $this->syncGroupFactory = $syncGroupFactory;
        $this->storageService = $storageService;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function registerWithDispatcher(EventDispatcherInterface $dispatcher): SyncGroupListener
    {
        $dispatcher->addListener(UserDeleteEvent::$NAME, [$this, 'onUserDelete']);
        $dispatcher->addListener(ParsePermissionEntityEvent::$NAME . 'syncGroup', [$this, 'onParsePermissions']);
        $dispatcher->addListener(FolderMovingEvent::$NAME, [$this, 'onFolderMoving']);

        return $this;
    }

    /**
     * @param UserDeleteEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @return void
     * @throws \Xibo\Support\Exception\GeneralException
     * @throws \Xibo\Support\Exception\NotFoundException
     */
    public function onUserDelete(UserDeleteEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $user = $event->getUser();
        $function = $event->getFunction();
        $newUser = $event->getNewUser();

        if ($function === 'delete') {
            // we do not want to delete Display specific Display Groups, reassign to systemUser instead.
            foreach ($this->syncGroupFactory->getByOwnerId($user->userId) as $syncGroup) {
                $syncGroup->delete();
            }
        } else if ($function === 'reassignAll') {
            foreach ($this->syncGroupFactory->getByOwnerId($user->userId) as $syncGroup) {
                $syncGroup->setOwner($newUser->getOwnerId());
                $syncGroup->save();
            }
        } else if ($function === 'countChildren') {
            $syncGroups = $this->syncGroupFactory->getByOwnerId($user->userId);

            $count = count($syncGroups);
            $this->getLogger()->debug(
                sprintf(
                    'Counted Children Sync Groups on User ID %d, there are %d',
                    $user->userId,
                    $count
                )
            );

            $event->setReturnValue($event->getReturnValue() + $count);
        }
    }

    /**
     * @param ParsePermissionEntityEvent $event
     * @return void
     * @throws \Xibo\Support\Exception\NotFoundException
     */
    public function onParsePermissions(ParsePermissionEntityEvent $event)
    {
        $this->getLogger()->debug('onParsePermissions');
        $event->setObject($this->syncGroupFactory->getById($event->getObjectId()));
    }

    /**
     * @param FolderMovingEvent $event
     * @return void
     * @throws \Xibo\Support\Exception\NotFoundException
     */
    public function onFolderMoving(FolderMovingEvent $event)
    {
        $folder = $event->getFolder();
        $newFolder = $event->getNewFolder();

        foreach ($this->syncGroupFactory->getByFolderId($folder->getId()) as $syncGroup) {
            $syncGroup->folderId = $newFolder->getId();
            $syncGroup->permissionsFolderId = $newFolder->getPermissionFolderIdOrThis();
            $syncGroup->updateFolders('syncgroup');
        }
    }
}
