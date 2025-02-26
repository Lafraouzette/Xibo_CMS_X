<?php


namespace Xibo\XMR;

/**
 * Class ScheduleCriteriaUpdateAction
 * @package Xibo\XMR
 */
class ScheduleCriteriaUpdateAction extends PlayerAction
{
    /**
     * @var array
     */
    public $criteriaUpdates = [];

    public function __construct()
    {
        $this->setQos(10);
    }

    /**
     * Set criteria updates
     * @param array $criteriaUpdates an array of criteria updates
     * @return $this
     */
    public function setCriteriaUpdates(array $criteriaUpdates)
    {
        $this->criteriaUpdates = $criteriaUpdates;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMessage(): string
    {
        $this->action = 'criteriaUpdate';

        // Ensure criteriaUpdates array is not empty
        if (empty($this->criteriaUpdates)) {
            // Throw an exception if criteriaUpdates is not provided
            throw new PlayerActionException(__('Criteria updates not provided.'));
        }

        return $this->serializeToJson(['criteriaUpdates']);
    }
}
