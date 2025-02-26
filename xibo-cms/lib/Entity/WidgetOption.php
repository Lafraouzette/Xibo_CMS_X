<?php



namespace Xibo\Entity;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;


/**
 * Class WidgetOption
 * @package Xibo\Entity
 *
 * @SWG\Definition()
 */
class WidgetOption implements \JsonSerializable
{
    use EntityTrait;

    /**
     * @SWG\Property(description="The Widget ID that this Option belongs to")
     * @var int
     */
    public $widgetId;

    /**
     * @SWG\Property(description="The option type, either attrib or raw")
     * @var string
     */
    public $type;

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

    public function __clone()
    {
        $this->widgetId = null;
    }

    public function __toString()
    {
        if ($this->type == 'cdata') {
            return sprintf('%s WidgetOption %s', $this->type, $this->option);
        }
        else {
            return sprintf('%s WidgetOption %s with value %s', $this->type, $this->option, $this->value);
        }
    }

    public function save()
    {
        $this->getLog()->debug('Saving ' . $this);

        $this->getStore()->insert('
            INSERT INTO `widgetoption` (`widgetId`, `type`, `option`, `value`)
              VALUES (:widgetId, :type, :option, :value) ON DUPLICATE KEY UPDATE `value` = :value2
        ', array(
            'widgetId' => $this->widgetId,
            'type' => $this->type,
            'option' => $this->option,
            'value' => $this->value,
            'value2' => $this->value
        ));
    }

    public function delete()
    {
        $this->getStore()->update('DELETE FROM `widgetoption` WHERE `widgetId` = :widgetId AND `option` = :option', array(
            'widgetId' => $this->widgetId, 'option' => $this->option)
        );
    }
}