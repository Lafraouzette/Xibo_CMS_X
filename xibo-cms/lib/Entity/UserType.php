<?php



namespace Xibo\Entity;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;

/**
 * Class UserType
 * @package Xibo\Entity
 *
 */
class UserType
{
    use EntityTrait;

    public $userTypeId;
    public $userType;

    /**
     * Entity constructor.
     * @param StorageServiceInterface $store
     * @param LogServiceInterface $log
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct($store, $log, $dispatcher)
    {
        $this->setCommonDependencies($store, $log, $dispatcher);
    }

    public function getId()
    {
        return $this->userTypeId;
    }

    public function getOwnerId()
    {
        return 1;
    }
}