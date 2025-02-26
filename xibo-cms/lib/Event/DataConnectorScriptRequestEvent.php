<?php


namespace Xibo\Event;

use Xibo\Connector\DataConnectorScriptProviderInterface;
use Xibo\Entity\DataSet;

/**
 * Event triggered to retrieve the Data Connector JavaScript from a connector.
 */
class DataConnectorScriptRequestEvent extends Event implements DataConnectorScriptProviderInterface
{
    public static $NAME = 'data.connector.script.request';

    /**
     * @var DataSet
     */
    private $dataSet;

    /**
     * @param DataSet $dataSet
     */
    public function __construct(DataSet $dataSet)
    {
        $this->dataSet = $dataSet;
    }

    /**
     * @inheritDoc
     */
    public function getConnectorId(): string
    {
        return $this->dataSet->dataConnectorSource;
    }

    /**
     * @inheritDoc
     */
    public function setScript(string $script): void
    {
        if ($this->dataSet->isRealTime == 0) {
            return;
        }

        // Save the script.
        $this->dataSet->saveScript($script);
    }
}
