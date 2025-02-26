<?php


namespace Xibo\Connector;

/**
 * Interface for handling the DataConnectorScriptRequestEvent.
 *
 * Provides methods for connectors to supply their data connector JS code.
 *
 * These methods should be used together:
 * - Use getConnectorId() to retrieve the unique identifier of the connector provided in the event.
 * - Check if the connector's ID matches the ID provided in the event.
 * - If the IDs match, use setScript() to provide the JavaScript code for the data connector.
 *
 * This ensures that the correct script is supplied by the appropriate connector.
 */
interface DataConnectorScriptProviderInterface
{
    /**
     * Get the unique identifier of the connector that is selected as the data source for the dataset.
     *
     * @return string
     */
    public function getConnectorId(): string;

    /**
     * Set the data connector JavaScript code provided by the connector. Requires real time.
     *
     * @param string $script JavaScript code
     * @return void
     */
    public function setScript(string $script): void;
}
