<?php



namespace Xibo\Factory;


use Xibo\Entity\ApplicationRedirectUri;
use Xibo\Support\Exception\NotFoundException;

/**
 * Class ApplicationRedirectUriFactory
 * @package Xibo\Factory
 */
class ApplicationRedirectUriFactory extends BaseFactory
{
    /**
     * Create Empty
     * @return ApplicationRedirectUri
     */
    public function create()
    {
        return new ApplicationRedirectUri($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Get by ID
     * @param $id
     * @return ApplicationRedirectUri
     * @throws NotFoundException
     */
    public function getById($id)
    {
        $clientRedirectUri = $this->query(null, ['id' => $id]);

        if (count($clientRedirectUri) <= 0)
            throw new NotFoundException();

        return $clientRedirectUri[0];
    }

    /**
     * Get by Client Id
     * @param $clientId
     * @return array[ApplicationRedirectUri]
     */
    public function getByClientId($clientId)
    {
        return $this->query(null, ['clientId' => $clientId]);
    }

    /**
     * Query
     * @param null $sortOrder
     * @param array $filterBy
     * @return array
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $entries = [];
        $params = [];

        $sanitizedFilter = $this->getSanitizer($filterBy);

        $select = 'SELECT id, client_id AS clientId, redirect_uri AS redirectUri ';

        $body = ' FROM `oauth_client_redirect_uris` WHERE 1 = 1 ';

        if ($sanitizedFilter->getString('clientId') != null) {
            $body .= ' AND `oauth_client_redirect_uris`.client_id = :clientId ';
            $params['clientId'] = $sanitizedFilter->getString('clientId');
        }

        if ($sanitizedFilter->getString('id') != null) {
            $body .= ' AND `oauth_client_redirect_uris`.client_id = :id ';
            $params['id'] = $sanitizedFilter->getString('id');
        }

        // Sorting?
        $order = '';
        if (is_array($sortOrder)) {
            $order .= 'ORDER BY ' . implode(',', $sortOrder);
        }

        $limit = '';
        // Paging
        if ($filterBy !== null && $sanitizedFilter->getInt('start') !== null && $sanitizedFilter->getInt('length') !== null) {
            $limit = ' LIMIT ' . $sanitizedFilter->getInt('start', ['default' => 0]) . ', ' . $sanitizedFilter->getInt('length', ['default' => 10]);
        }

        // The final statements
        $sql = $select . $body . $order . $limit;



        foreach ($this->getStore()->select($sql, $params) as $row) {
            $entries[] = $this->create()->hydrate($row);
        }

        // Paging
        if ($limit != '' && count($entries) > 0) {
            $results = $this->getStore()->select('SELECT COUNT(*) AS total ' . $body, $params);
            $this->_countLast = intval($results[0]['total']);
        }

        return $entries;
    }
}