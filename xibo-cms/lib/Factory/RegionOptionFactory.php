<?php



namespace Xibo\Factory;


use Xibo\Entity\RegionOption;

/**
 * Class RegionOptionFactory
 * @package Xibo\Factory
 */
class RegionOptionFactory extends BaseFactory
{
    /**
     * @return RegionOption
     */
    public function createEmpty()
    {
        return new RegionOption($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Load by Region Id
     * @param int $regionId
     * @return array[RegionOption]
     */
    public function getByRegionId($regionId)
    {
        return $this->query(null, array('regionId' => $regionId));
    }

    /**
     * Create a region option
     * @param int $regionId
     * @param string $option
     * @param mixed $value
     * @return RegionOption
     */
    public function create($regionId, $option, $value)
    {
        $regionOption = $this->createEmpty();
        $regionOption->regionId = $regionId;
        $regionOption->option = $option;
        $regionOption->value = $value;

        return $regionOption;
    }

    /**
     * Query Region options
     * @param array $sortOrder
     * @param array $filterBy
     * @return array[RegionOption]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $sanitizedFilter = $this->getSanitizer($filterBy);

        $sql = 'SELECT * FROM `regionoption` WHERE regionId = :regionId';

        foreach ($this->getStore()->select($sql, array('regionId' => $sanitizedFilter->getInt('regionId'))) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row);
        }

        return $entries;
    }
}