<?php


namespace Xibo\Listener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Xibo\Event\FolderMovingEvent;
use Xibo\Event\ParsePermissionEntityEvent;
use Xibo\Event\TagDeleteEvent;
use Xibo\Event\TriggerTaskEvent;
use Xibo\Event\UserDeleteEvent;
use Xibo\Factory\PlaylistFactory;
use Xibo\Storage\StorageServiceInterface;

/**
 * Playlist events
 */
class PlaylistListener
{
    use ListenerLoggerTrait;

    /**
     * @var PlaylistFactory
     */
    private $playlistFactory;
    /**
     * @var StorageServiceInterface
     */
    private $storageService;

    /**
     * @param PlaylistFactory $playlistFactory
     * @param StorageServiceInterface $storageService
     */
    public function __construct(
        PlaylistFactory $playlistFactory,
        StorageServiceInterface $storageService
    ) {
        $this->playlistFactory = $playlistFactory;
        $this->storageService = $storageService;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function registerWithDispatcher(EventDispatcherInterface $dispatcher) : PlaylistListener
    {
        $dispatcher->addListener(UserDeleteEvent::$NAME, [$this, 'onUserDelete']);
        $dispatcher->addListener(ParsePermissionEntityEvent::$NAME . 'playlist', [$this, 'onParsePermissions']);
        $dispatcher->addListener(FolderMovingEvent::$NAME, [$this, 'onFolderMoving']);
        $dispatcher->addListener(TagDeleteEvent::$NAME, [$this, 'onTagDelete']);

        return $this;
    }

    /**
     * @param UserDeleteEvent $event
     * @return void
     * @throws \Xibo\Support\Exception\DuplicateEntityException
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     * @throws \Xibo\Support\Exception\NotFoundException
     */
    public function onUserDelete(UserDeleteEvent $event)
    {
        $user = $event->getUser();
        $function = $event->getFunction();
        $newUser = $event->getNewUser();

        if ($function === 'delete') {
            // Delete Playlists owned by this user
            foreach ($this->playlistFactory->getByOwnerId($user->userId) as $playlist) {
                $playlist->delete();
            }
        } else if ($function === 'reassignAll') {
            // Reassign playlists and widgets
            foreach ($this->playlistFactory->getByOwnerId($user->userId) as $playlist) {
                $playlist->setOwner($newUser->userId);
                $playlist->save();
            }
        } else if ($function === 'countChildren') {
            $playlists = $this->playlistFactory->getByOwnerId($user->userId);

            $count = count($playlists);
            $this->getLogger()->debug(
                sprintf(
                    'Counted Children Playlists on User ID %d, there are %d',
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
        $event->setObject($this->playlistFactory->getById($event->getObjectId()));
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

        foreach ($this->playlistFactory->getbyFolderId($folder->getId()) as $playlist) {
            $playlist->folderId = $newFolder->getId();
            $playlist->permissionsFolderId = $newFolder->getPermissionFolderIdOrThis();
            $playlist->updateFolders('playlist');
        }
    }

    /**
     * @param TagDeleteEvent $event
     * @param $eventName
     * @param EventDispatcherInterface $dispatcher
     * @return void
     */
    public function onTagDelete(TagDeleteEvent $event, $eventName, EventDispatcherInterface $dispatcher)
    {
        $this->storageService->update(
            'DELETE FROM `lktagplaylist` WHERE `lktagplaylist`.tagId = :tagId',
            ['tagId' => $event->getTagId()]
        );

        $dispatcher->dispatch(new TriggerTaskEvent('\Xibo\XTR\DynamicPlaylistSyncTask'), TriggerTaskEvent::$NAME);
    }
}
