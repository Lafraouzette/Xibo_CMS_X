<?php



namespace Xibo\Factory;


use Xibo\Entity\DataType;
use Xibo\Support\Exception\NotFoundException;

/**
 * Class DataTypeFactory
 * @package Xibo\Factory
 */
class DataTypeFactory extends BaseFactory
{
    /**
     * @return DataType
     */
    public function createEmpty()
    {
        return new DataType($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Get By Id
     * @param int $id
     * @return DataType
     * @throws NotFoundException
     */
    public function getById($id)
    {
        $results = $this->query(null, ['dataTypeId' => $id]);

        if (count($results) <= 0)
            throw new NotFoundException();

        return $results[0];
    }

    /**
     * @param null $sortOrder
     * @param array $filterBy
     * @return array[DataType]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $sanitizedFilter = $this->getSanitizer($filterBy);

        $params = [];
        $sql = 'SELECT dataTypeId, dataType FROM `datatype` WHERE 1 = 1 ';

        if ($sanitizedFilter->getInt('dataTypeId') !== null) {
            $sql .= ' AND `datatype`.dataTypeId = :dataTypeId ';
            $params['dataTypeId'] = $sanitizedFilter->getInt('dataTypeId');
        }

        foreach ($this->getStore()->select($sql, $params) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row);
        }

        return $entries;
    }
}