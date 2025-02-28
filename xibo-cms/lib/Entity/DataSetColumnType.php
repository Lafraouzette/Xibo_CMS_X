<?php



namespace Xibo\Entity;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;

/**
 * Class DataSetColumnType
 * @package Xibo\Entity
 *
 * @SWG\Definition()
 */
class DataSetColumnType implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="The ID for this DataSetColumnType")
     * @var int
     */
    public $dataSetColumnTypeId;

    /**
     * @SWG\Property(description="The name for this DataSetColumnType")
     * @var string
     */
    public $dataSetColumnType;

    /**
     * Entity constructor.
     * @param StorageServiceInterface $store
     * @param LogServiceInterface $log
     */
    public function __construct($store, $log, $dispatcher)
    {
        $this->setCommonDependencies($store, $log, $dispatcher);
    }
}
