<?php


namespace Xibo\Event;

use InvalidArgumentException;
use Xibo\Connector\DataConnectorSourceProviderInterface;

/**
 * Event triggered to retrieve a list of data connector sources.
 *
 * This event collects metadata (names and IDs) of connectors that provides data connector.
 */
class DataConnectorSourceRequestEvent extends Event implements DataConnectorSourceProviderInterface
{
    public static $NAME = 'data.connector.source.request';

    /**
     * @var array
     */
    private $dataConnectorSources = [];

    /**
     * Initializes the dataConnectorSources with default value.
     */
    public function __construct()
    {
        $this->dataConnectorSources[] = [
            'id' => 'user_defined',
            'name' => __('User-Defined JavaScript')
        ];
    }

    /**
     * @inheritDoc
     */
    public function addDataConnectorSource(string $id, string $name): void
    {
        // ensure that there are no duplicate id or name
        foreach ($this->dataConnectorSources as $dataConnectorSource) {
            if ($dataConnectorSource['id'] == $id) {
                throw new InvalidArgumentException('Duplicate Connector ID found.');
            }
            if ($dataConnectorSource['name'] == $name) {
                throw new InvalidArgumentException('Duplicate Connector Name found.');
            }
        }

        $this->dataConnectorSources[] = ['id' => $id, 'name' => $name];
    }

    /**
     * Retrieves the list of data connector sources.
     *
     * @return array
     */
    public function getDataConnectorSources(): array
    {
        return $this->dataConnectorSources;
    }
}
