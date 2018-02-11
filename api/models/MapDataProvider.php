<?php
/**
 * Created by PhpStorm.
 * User: mzapeka
 * Date: 10.02.18
 * Time: 22:29
 */

namespace api\models;


use yii\base\BaseObject;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\data\Sort;

/**
 * @property int $count
 * @property array $keys
 * @property array $models
 * @property Pagination|false $pagination
 * @property Sort|bool $sort
 * @property int $totalCount
 */
class MapDataProvider extends BaseObject implements DataProviderInterface
{

    private $provider;
    private $callback;

    public function __construct(DataProviderInterface $provider, callable $callback)
    {
        $this->provider = $provider;
        $this->callback = $callback;
        parent::__construct();
    }

    /**
     * Prepares the data models and keys.
     *
     * This method will prepare the data models and keys that can be retrieved via
     * [[getModels()]] and [[getKeys()]].
     *
     * This method will be implicitly called by [[getModels()]] and [[getKeys()]] if it has not been called before.
     *
     * @param bool $forcePrepare whether to force data preparation even if it has been done before.
     */
    public function prepare($forcePrepare = false):void
    {
        $this->provider->prepare($forcePrepare);
    }

    /**
     * Returns the number of data models in the current page.
     * This is equivalent to `count($provider->getModels())`.
     * When [[getPagination|pagination]] is false, this is the same as [[getTotalCount|totalCount]].
     * @return int the number of data models in the current page.
     */
    public function getCount(): int
    {
        return $this->provider->getCount();
    }

    /**
     * Returns the total number of data models.
     * When [[getPagination|pagination]] is false, this is the same as [[getCount|count]].
     * @return int total number of possible data models.
     */
    public function getTotalCount(): int
    {
        return $this->provider->getTotalCount();
    }

    /**
     * Returns the data models in the current page.
     * @return array the list of data models in the current page.
     */
    public function getModels(): array
    {
        return array_map($this->callback, $this->provider->getModels());
    }

    /**
     * Returns the key values associated with the data models.
     * @return array the list of key values corresponding to [[getModels|models]]. Each data model in [[getModels|models]]
     * is uniquely identified by the corresponding key value in this array.
     */
    public function getKeys(): array
    {
        return $this->provider->getKeys();
    }

    /**
     * @return Sort the sorting object. If this is false, it means the sorting is disabled.
     */
    public function getSort()
    {
        return $this->provider->getSort();
    }

    /**
     * @return Pagination|false the pagination object. If this is false, it means the pagination is disabled.
     */
    public function getPagination()
    {
        return $this->provider->getPagination();
    }
}