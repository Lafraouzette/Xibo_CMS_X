<?php



namespace Xibo\Factory;


use Xibo\Entity\DataSetColumnType;
use Xibo\Support\Exception\NotFoundException;

/**
 * Class DataSetColumnTypeFactory
 * @package Xibo\Factory
 */
class DataSetColumnTypeFactory extends BaseFactory
{
    /**
     * @return DataSetColumnType
     */
    public function createEmpty()
    {
        return new DataSetColumnType($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Get By Id
     * @param int $id
     * @return DataSetColumnType
     * @throws NotFoundException
     */
    public function getById($id)
    {
        $results = $this->query(null, ['dataSetColumnTypeId' => $id]);

        if (count($results) <= 0)
            throw new NotFoundException();

        return $results[0];
    }

    /**
     * @param null $sortOrder
     * @param array $filterBy
     * @return array[DataSetColumnType]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $params = [];
        $sanitizedFilter = $this->getSanitizer($filterBy);
        
        $sql = 'SELECT dataSetColumnTypeId, dataSetColumnType FROM `datasetcolumntype` WHERE 1 = 1 ';

        if ($sanitizedFilter->getInt('dataSetColumnTypeId') !== null) {
            $sql .= ' AND `datasetcolumntype`.dataSetColumnTypeId = :dataSetColumnTypeId ';
            $params['dataSetColumnTypeId'] = $sanitizedFilter->getInt('dataSetColumnTypeId');
        }

        foreach ($this->getStore()->select($sql, $params) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row);
        }

        return $entries;
    }
}