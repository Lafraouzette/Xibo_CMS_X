<?php


namespace Xibo\Entity;

use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;

/**
 * @SWG\Definition()
 */
class PlayerFault implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="The Fault Id")
     * @var int
     */
    public $playerFaultId;

    /**
     * @SWG\Property(description="The Display Id")
     * @var int
     */
    public $displayId;

    /**
     * @SWG\Property(description="The Date the error occured")
     * @var string
     */
    public $incidentDt;

    /**
     * @SWG\Property(description="The Date the error expires")
     * @var string
     */
    public $expires;

    /**
     * @SWG\Property(description="The Code associated with the fault")
     * @var int
     */
    public $code;

    /**
     * @SWG\Property(description="The Reason for the fault")
     * @var string
     */
    public $reason;

    /**
     * @SWG\Property(description="The Layout Id")
     * @var int
     */
    public $layoutId;

    /**
     * @SWG\Property(description="The Region Id")
     * @var int
     */
    public $regionId;

    /**
     * @SWG\Property(description="The Schedule Id")
     * @var int
     */
    public $scheduleId;

    /**
     * @SWG\Property(description="The Widget Id")
     * @var int
     */
    public $widgetId;

    /**
     * @SWG\Property(description="The Media Id")
     * @var int
     */
    public $mediaId;

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
     * @return string
     */
    public function __toString()
    {
        return sprintf('Player Fault Id %d, Code %d, Reason %s, Date %s', $this->playerFaultId, $this->code, $this->reason, $this->incidentDt);
    }
}
