<?php



namespace Xibo\Factory;

use Xibo\Entity\DisplayType;
use Xibo\Support\Exception\NotFoundException;

/**
 * Class DisplayTypeFactory
 * @package Xibo\Factory
 */
class DisplayTypeFactory extends BaseFactory
{
    /**
     * @return DisplayType
     */
    public function createEmpty()
    {
        return new DisplayType($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Get By Id
     * @param int $id
     * @return DisplayType
     * @throws NotFoundException
     */
    public function getById($id)
    {
        $results = $this->query(null, ['displayTypeId' => $id]);

        if (count($results) <= 0) {
            throw new NotFoundException();
        }

        return $results[0];
    }

    /**
     * @param null $sortOrder
     * @param array $filterBy
     * @return DisplayType[]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $sanitizedFilter = $this->getSanitizer($filterBy);

        $params = [];
        $sql = 'SELECT displayTypeId, displayType FROM `display_types` WHERE 1 = 1 ';

        if ($sanitizedFilter->getInt('displayTypeId') !== null) {
            $sql .= ' AND `display_type`.displayTypeId = :displayTypeId ';
            $params['displayTypeId'] = $sanitizedFilter->getInt('displayTypeId');
        }

        foreach ($this->getStore()->select($sql, $params) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row);
        }

        return $entries;
    }
}
