<?php

namespace JustCoded\BackInStockConfigurable\Model;

use JustCoded\BackInStockConfigurable\Helper\Data;
use JustCoded\BackInStockConfigurable\Model\ResourceModel\Subscription as SubscriptionResource;
use JustCoded\BackInStockConfigurable\Model\ResourceModel\Subscription\Collection;
use JustCoded\BackInStockConfigurable\Model\SubscriptionInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\DataObject;

class Subscription extends AbstractModel implements SubscriptionInterface
{
    const CACHE_TAG = 'justcoded_backinstock_subscription';

    /**
     * @var Data
     */
    private $moduleHelper;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    public function __construct(
        Context $context,
        Registry $registry,
        SubscriptionResource $resource,
        Collection $resourceCollection,
        Data $moduleHelper,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('JustCoded\BackInStockConfigurable\Model\ResourceModel\Subscription');
    }

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    public function proceed()
    {
        $this->moduleHelper->sendMail($this);
    }

    /**
     * @inheritdoc
     */
    public function getProduct()
    {
        return $this->productRepository->getById($this->getProductId());
    }

    /**
     * @inheritdoc
     */
    public function isReady($product = null)
    {
        if (!$product) {
            $product = $this->getProduct();
        }

        $stockData = $product->getStockData();

        if ($stockData) {
            $stockItem = new DataObject($stockData);
        } else {
            $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        }

        return 0 < $stockItem->getQty() && $stockItem->getIsInStock();
    }
}
