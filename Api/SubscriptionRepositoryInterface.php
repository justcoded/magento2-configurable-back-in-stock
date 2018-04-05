<?php

namespace JustCoded\BackInStockConfigurable\Api;

use JustCoded\BackInStockConfigurable\Model\SubscriptionInterface as Subscription;
use Magento\Framework\Api\SearchCriteriaInterface as SearchCriteria;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterface as SearchResults;

interface SubscriptionRepositoryInterface 
{
    /**
     * @param Subscription $object
     * @return SubscriptionInterface
     * @throws CouldNotSaveException
     */
    public function save(Subscription $object);

    /**
     * @param $id
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param SearchCriteria $criteria
     * @return SearchResults
     */
    public function getList(SearchCriteria $criteria);

    /**
     * @param Subscription $object
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Subscription $object);

    /**
     * @param $id
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * @param $email
     * @param $productId
     * @return Subscription|null
     */
    public function getByEmailAndProductId($email, $productId);
}
