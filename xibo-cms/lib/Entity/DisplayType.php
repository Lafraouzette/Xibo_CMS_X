<?php



namespace Xibo\Entity;

use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;

/**
 * Class DisplayType
 * @package Xibo\Entity
 *
 * @SWG\Definition()
 */
class DisplayType implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="The ID for this DisplayType")
     * @var int
     */
    public $displayTypeId;

    /**
     * @SWG\Property(description="The Name for this DisplayType")
     * @var string
     */
    public $displayType;

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
}
