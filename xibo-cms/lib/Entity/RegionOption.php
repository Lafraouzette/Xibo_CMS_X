<?php



namespace Xibo\Entity;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;

/**
 * Class RegionOption
 * @package Xibo\Entity
 *
 * @SWG\Definition()
 */
class RegionOption implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="The regionId that this Option applies to")
     * @var int
     */
    public $regionId;

    /**
     * @SWG\Property(description="The option name")
     * @var string
     */
    public $option;

    /**
     * @SWG\Property(description="The option value")
     * @var string
     */
    public $value;

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

    /**
     * Clone
     */
    public function __clone()
    {
        $this->regionId = null;
    }

    public function save()
    {
        $sql = 'INSERT INTO `regionoption` (`regionId`, `option`, `value`) VALUES (:regionId, :option, :value) ON DUPLICATE KEY UPDATE `value` = :value2';
        $this->getStore()->insert($sql, array(
            'regionId' => $this->regionId,
            'option' => $this->option,
            'value' => $this->value,
            'value2' => $this->value,
        ));
    }

    public function delete()
    {
        $sql = 'DELETE FROM `regionoption` WHERE `regionId` = :regionId AND `option` = :option';
        $this->getStore()->update($sql, array('regionId' => $this->regionId, 'option' => $this->option));
    }
}