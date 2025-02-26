<?php


namespace Xibo\Entity;

use Xibo\Connector\ConnectorInterface;
use Xibo\Service\LogServiceInterface;
use Xibo\Storage\StorageServiceInterface;
use Xibo\Support\Exception\InvalidArgumentException;

/**
 * Represents the database object for a Connector
 *
 * @SWG\Definition()
 */
class Connector implements \JsonSerializable
{
    use EntityTrait;

    // Status properties
    public $isInstalled = true;
    public $isSystem = true;

    // Database properties
    public $connectorId;
    public $className;
    public $settings;
    public $isEnabled;
    public $isVisible;

    // Decorated properties
    public $title;
    public $description;
    public $thumbnail;

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
     * @param \Xibo\Connector\ConnectorInterface $connector
     * @return $this
     */
    public function decorate(ConnectorInterface $connector): Connector
    {
        $this->title = $connector->getTitle();
        $this->description = $connector->getDescription();
        $this->thumbnail = $connector->getThumbnail();
        if (empty($this->thumbnail)) {
            $this->thumbnail = 'theme/default/img/connectors/placeholder.png';
        }
        return $this;
    }

    public function save()
    {
        if ($this->connectorId == null || $this->connectorId == 0) {
            $this->add();
        } else {
            $this->edit();
        }
    }

    private function add()
    {
        $this->connectorId = $this->getStore()->insert('
          INSERT INTO `connectors` (`className`, `isEnabled`, `isVisible`, `settings`)
            VALUES (:className, :isEnabled, :isVisible, :settings)
        ', [
            'className' => $this->className,
            'isEnabled' => $this->isEnabled,
            'isVisible' => $this->isVisible,
            'settings' => json_encode($this->settings)
        ]);
    }

    private function edit()
    {
        $this->getStore()->update('
          UPDATE `connectors` SET
              `className` = :className,
              `isEnabled` = :isEnabled,
              `isVisible` = :isVisible,
              `settings` = :settings
           WHERE connectorId = :connectorId
        ', [
            'connectorId' => $this->connectorId,
            'className' => $this->className,
            'isEnabled' => $this->isEnabled,
            'isVisible' => $this->isVisible,
            'settings' => json_encode($this->settings)
        ]);
    }

    /**
     * @return void
     * @throws \Xibo\Support\Exception\InvalidArgumentException
     */
    public function delete()
    {
        if ($this->isSystem) {
            throw new InvalidArgumentException(__('Sorry we cannot delete a system connector.'), 'isSystem');
        }

        $this->getStore()->update('DELETE FROM `connectors` WHERE connectorId = :connectorId', [
            'connectorId' => $this->connectorId
        ]);
    }
}
