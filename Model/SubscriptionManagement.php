<?php

namespace JustCoded\BackInStockConfigurable\Model;

use JustCoded\BackInStockConfigurable\Api\SubscriptionInterface;
use JustCoded\BackInStockConfigurable\Api\SubscriptionRepositoryInterface as SubscriptionRepository;
use Magento\Catalog\Api\Data\ProductInterface as Product;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class SubscriptionManagement implements SubscriptionInterface
{
    /**
     * @var SubscriptionRepository
     */
    protected $subscriptionRepository;

    /**
     * @var SubscriptionFactory
     */
    protected $subscriptionFactory;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        SubscriptionFactory $subscriptionFactory,
        ProductRepository $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        JsonHelper $jsonHelper,
        StoreManager $storeManager
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionFactory    = $subscriptionFactory;
        $this->productRepository      = $productRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->jsonHelper             = $jsonHelper;
        $this->storeManager           = $storeManager;
    }

    /**
     * @param array $simple
     * @param array $configurable
     *
     * @return Product[]
     */
    protected function findProductsByOptions($simple, $configurable)
    {
        $configurableProduct = $this->productRepository->getById($configurable['id']);

        return array_filter(
            $configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct),
            function ($product) use ($simple) {
                return $product->getId() === $simple['id'];
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function subscribe(
        $email,
        $simple,
        $configurable
    ) {
        $products = $this->findProductsByOptions(
            $this->jsonHelper->jsonDecode($simple),
            $this->jsonHelper->jsonDecode($configurable)
        );

        foreach ($products as $product) {
            if (!$product->getId()) {
                throw new NoSuchEntityException(__('Can\'t find product with such criteria.'));
            }

            if (!$this->subscriptionRepository->getByEmailAndProductId($email, $product->getId())) {
                $subscription = $this->subscriptionFactory->create()
                    ->setEmail($email)
                    ->setProductId($product->getId())
                    ->setStoreId($this->storeManager->getStore()->getId());

                $this->subscriptionRepository->save($subscription);
            }
        }

        return ['success' => true];
    }
}