<?php



namespace Xibo\Factory;


use Xibo\Entity\DataSetColumn;
use Xibo\Support\Exception\NotFoundException;

/**
 * Class DataSetColumnFactory
 * @package Xibo\Factory
 */
class DataSetColumnFactory extends BaseFactory
{
    /** @var  DataTypeFactory */
    private $dataTypeFactory;

    /** @var  DataSetColumnTypeFactory */
    private $dataSetColumnTypeFactory;

    /**
     * Construct a factory
     * @param DataTypeFactory $dataTypeFactory
     * @param DataSetColumnTypeFactory $dataSetColumnTypeFactory
     */
    public function __construct($dataTypeFactory, $dataSetColumnTypeFactory)
    {
        $this->dataTypeFactory = $dataTypeFactory;
        $this->dataSetColumnTypeFactory = $dataSetColumnTypeFactory;
    }

    /**
     * @return DataSetColumn
     */
    public function createEmpty()
    {
        return new DataSetColumn(
            $this->getStore(),
            $this->getLog(),
            $this->getDispatcher(),
            $this,
            $this->dataTypeFactory,
            $this->dataSetColumnTypeFactory
        );
    }

    /**
     * Get by Id
     * @param int $dataSetColumnId
     * @return DataSetColumn
     * @throws NotFoundException
     */
    public function getById($dataSetColumnId)
    {
        $columns = $this->query(null, ['dataSetColumnId' => $dataSetColumnId]);

        if (count($columns) <= 0)
            throw new NotFoundException();

        return $columns[0];
    }

    /**
     * Get by dataSetId
     * @param $dataSetId
     * @return DataSetColumn[]
     */
    public function getByDataSetId($dataSetId)
    {
        return $this->query(null, ['dataSetId' => $dataSetId]);
    }

    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $params = [];
        $sanitizedFilter = $this->getSanitizer($filterBy);

        if ($sortOrder == null)
            $sortOrder = ['columnOrder'];

        $select = '
            SELECT dataSetColumnId,
                dataSetId,
                heading,
                datatype.dataTypeId,
                datatype.dataType,
                datasetcolumn.dataSetColumnTypeId,
                datasetcolumntype.dataSetColumnType,
                listContent,
                columnOrder,
                formula,
                remoteField, 
                showFilter, 
                showSort,
                tooltip,
                isRequired,
                dateFormat
            ';

        $body = '
              FROM `datasetcolumn`
               INNER JOIN `datatype`
               ON datatype.DataTypeID = datasetcolumn.DataTypeID
               INNER JOIN `datasetcolumntype`
               ON datasetcolumntype.DataSetColumnTypeID = datasetcolumn.DataSetColumnTypeID
             WHERE 1 = 1 ';

        if ($sanitizedFilter->getInt('dataSetColumnId') !== null) {
            $body .= ' AND dataSetColumnId = :dataSetColumnId ';
            $params['dataSetColumnId'] = $sanitizedFilter->getInt('dataSetColumnId');
        }

        if ($sanitizedFilter->getInt('dataSetId') !== null) {
            $body .= ' AND DataSetID = :dataSetId ';
            $params['dataSetId'] = $sanitizedFilter->getInt('dataSetId');
        }

        if ($sanitizedFilter->getInt('remoteField') !== null) {
            $body .= ' AND remoteField = :remoteField ';
            $params['remoteField'] = $sanitizedFilter->getInt('remoteField');
        }

        // Sorting?
        $order = '';
        if (is_array($sortOrder))
            $order .= 'ORDER BY ' . implode(',', $sortOrder);

        $limit = '';
        // Paging
        if ($filterBy !== null && $sanitizedFilter->getInt('start') !== null && $sanitizedFilter->getInt('length') !== null) {
            $limit = ' LIMIT ' . $sanitizedFilter->getInt('start', ['default' => 0]) . ', ' . $sanitizedFilter->getInt('length', ['default' => 10]);
        }

        $sql = $select . $body . $order . $limit;


        foreach ($this->getStore()->select($sql, $params) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row, ['intProperties' => ['showFilter', 'showSort']]);
        }

        // Paging
        if ($limit != '' && count($entries) > 0) {
            $results = $this->getStore()->select('SELECT COUNT(*) AS total ' . $body, $params);
            $this->_countLast = intval($results[0]['total']);
        }

        return $entries;
    }
}