<?php


namespace Xibo\Entity;

use Respect\Validation\Validator as v;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;
use Xibo\Support\Exception\InvalidArgumentException;

/**
 * Class Resolution
 * @package Xibo\Entity
 *
 * @SWG\Definition()
 */
class Resolution implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="The ID of this Resolution")
     * @var int
     */
    public $resolutionId;

    /**
     * @SWG\Property(description="The resolution name")
     * @var string
     */
    public $resolution;

    /**
     * @SWG\Property(description="The display width of the resolution")
     * @var double
     */
    public $width;

    /**
     * @SWG\Property(description="The display height of the resolution")
     * @var double
     */
    public $height;

    /**
     * @SWG\Property(description="The designer width of the resolution")
     * @var double
     */
    public $designerWidth;

    /**
     * @SWG\Property(description="The designer height of the resolution")
     * @var double
     */
    public $designerHeight;

    /**
     * @SWG\Property(description="The layout schema version")
     * @var int
     */
    public $version = 2;

    /**
     * @SWG\Property(description="A flag indicating whether this resolution is enabled or not")
     * @var int
     */
    public $enabled = 1;

    /**
     * @SWG\Property(description="The userId who owns this Resolution")
     * @var int
     */
    public $userId;

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
     * @return int
     */
    public function getId()
    {
        return $this->resolutionId;
    }

    /**
     * @return int
     */
    public function getOwnerId()
    {
        // No owner
        return $this->userId;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validate()
    {
        if (!v::stringType()->notEmpty()->validate($this->resolution)) {
            throw new InvalidArgumentException(__('Please provide a name'), 'name');
        }

        if (!v::intType()->notEmpty()->min(1)->validate($this->width)) {
            throw new InvalidArgumentException(__('Please provide a width'), 'width');
        }

        if (!v::intType()->notEmpty()->min(1)->validate($this->height)) {
            throw new InvalidArgumentException(__('Please provide a height'), 'height');
        }

        // Set the designer width and height
        $factor = min (800 / $this->width, 800 / $this->height);

        $this->designerWidth = round($this->width * $factor);
        $this->designerHeight = round($this->height * $factor);
    }

    /**
     * Save
     * @param bool|true $validate
     * @throws InvalidArgumentException
     */
    public function save($validate = true)
    {
        if ($validate)
            $this->validate();

        if ($this->resolutionId == null || $this->resolutionId == 0)
            $this->add();
        else
            $this->edit();

        $this->getLog()->audit('Resolution', $this->resolutionId, 'Saving', $this->getChangedProperties());
    }

    public function delete()
    {
        $this->getStore()->update('DELETE FROM resolution WHERE resolutionID = :resolutionId', ['resolutionId' => $this->resolutionId]);
    }

    private function add()
    {
        $this->resolutionId = $this->getStore()->insert('
          INSERT INTO `resolution` (resolution, width, height, intended_width, intended_height, version, enabled, `userId`)
            VALUES (:resolution, :width, :height, :intended_width, :intended_height, :version, :enabled, :userId)
        ', [
            'resolution' => $this->resolution,
            'width' => $this->designerWidth,
            'height' => $this->designerHeight,
            'intended_width' => $this->width,
            'intended_height' => $this->height,
            'version' => $this->version,
            'enabled' => $this->enabled,
            'userId' => $this->userId
        ]);
    }

    private function edit()
    {
        $this->getStore()->update('
          UPDATE resolution SET resolution = :resolution,
                width = :width,
                height = :height,
                intended_width = :intended_width,
                intended_height = :intended_height,
                enabled = :enabled
           WHERE resolutionID = :resolutionId
        ', [
            'resolutionId' => $this->resolutionId,
            'resolution' => $this->resolution,
            'width' => $this->designerWidth,
            'height' => $this->designerHeight,
            'intended_width' => $this->width,
            'intended_height' => $this->height,
            'enabled' => $this->enabled
        ]);
    }
}