<?php



namespace Xibo\Entity;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;


/**
 * Class DataType
 * @package Xibo\Entity
 *
 * @SWG\Definition()
 */
class DataType implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="The ID for this DataType")
     * @var int
     */
    public $dataTypeId;

    /**
     * @SWG\Property(description="The Name for this DataType")
     * @var string
     */
    public $dataType;

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
