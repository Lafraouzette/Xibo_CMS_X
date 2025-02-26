<?php


namespace Xibo\Connector;

use InvalidArgumentException;

/**
 * Interface for handling the DataConnectorSourceRequestEvent.
 *
 * Registers connectors that provide data connector JavaScript (JS).
 */
interface DataConnectorSourceProviderInterface
{
    /**
     * Adds/Registers a connector, that would provide a data connector JS, to the event.
     * Implementations should use $this->getSourceName() as the $id and $this->getTitle() as the $name.
     *
     * @param string $id
     * @param string $name
     * @throws InvalidArgumentException if a duplicate ID or name is found.
     */
    public function addDataConnectorSource(string $id, string $name): void;
}
