<?php



namespace Xibo\Factory;


use Xibo\Entity\UserOption;

/**
 * Class UserOptionFactory
 * @package Xibo\Factory
 */
class UserOptionFactory extends BaseFactory
{
    /**
     * Load by User Id
     * @param int $userId
     * @return array[UserOption]
     */
    public function getByUserId($userId)
    {
        return $this->query(null, array('userId' => $userId));
    }

    /**
     * Create Empty
     * @return UserOption
     */
    public function createEmpty()
    {
        return new UserOption($this->getStore(), $this->getLog(), $this->getDispatcher());
    }

    /**
     * Create a user option
     * @param int $userId
     * @param string $option
     * @param mixed $value
     * @return UserOption
     */
    public function create($userId, $option, $value)
    {
        $userOption = $this->createEmpty();
        $userOption->userId = $userId;
        $userOption->option = $option;
        $userOption->value = $value;

        return $userOption;
    }

    /**
     * Query User options
     * @param array $sortOrder
     * @param array $filterBy
     * @return array[UserOption]
     */
    public function query($sortOrder = null, $filterBy = [])
    {
        $parsedFilter = $this->getSanitizer($filterBy);
        $entries = [];

        $sql = 'SELECT * FROM `useroption` WHERE userId = :userId';

        foreach ($this->getStore()->select($sql,['userId' => $parsedFilter->getInt('userId')]) as $row) {
            $entries[] = $this->createEmpty()->hydrate($row);
        }

        return $entries;
    }
}