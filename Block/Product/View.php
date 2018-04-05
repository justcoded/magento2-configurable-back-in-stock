<?php

namespace JustCoded\BackInStockConfigurable\Block\Product;

use JustCoded\BackInStockConfigurable\Helper\Data as ModuleHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\View as ProductView;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\CatalogInventory\Api\StockStateInterface as StockState;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session;
use Magento\Framework\Json\EncoderInterface as JsonEncoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface as UrlEncoderInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Api\Data\ProductInterface as Product;

class View extends ProductView
{
    /**
     * @var StockState
     */
    protected $stockItem;

    /**
     * @var ModuleHelper
     */
    protected $moduleHelper;

    public function __construct(
        Context $context,
        UrlEncoderInterface $urlEncoder,
        JsonEncoderInterface $jsonEncoder,
        StringUtils $string,
        ProductHelper $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        StockState $stockItem,
        ModuleHelper $moduleHelper,
        array $data = []
    ) {
        $this->stockItem    = $stockItem;
        $this->moduleHelper = $moduleHelper;

        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $data);
    }

    /**
     * @return string
     */
    public function getServiceUrl()
    {
        $storeCode = $this->_storeManager->getStore()->getCode();

        return '/rest/' . $storeCode . '/V1/in-stock-notify/:email/subscribe';
    }

    /**
     * @return string
     */
    public function getHeaderContent()
    {
        $blockId = $this->moduleHelper->getPopupHeaderCmsBlockId();

        if (!$blockId) {
            return '';
        }

        return $this->getLayout()
            ->createBlock(\Magento\Cms\Block\Block::class)
            ->setBlockId($blockId)
            ->toHtml();
    }

    /**
     * @return array
     */
    public function getConfigurableAttributes()
    {
        $sortingOrder = $this->moduleHelper->getAttributesSortingOrder();

        $attributes = $this->getProduct()->getTypeInstance()->getUsedProductAttributes($this->getProduct());

        usort($attributes, function ($a, $b) use ($sortingOrder) {
            return array_search($a->getAttributeCode(), $sortingOrder) - array_search($b->getAttributeCode(), $sortingOrder);
        });

        return $attributes;
    }

    public function getConfigurableAttributeOptions($attribute)
    {
        $options = [];

        foreach ($this->getOutOfStockSimpleProducts() as $product) {
            if (!isset($options[$product->getData($attribute->getAttributeCode())])) {
                $options[$product->getData($attribute->getAttributeCode())] = [
                    'label' => $product->getResource()->getAttribute($attribute->getAttributeCode())
                        ->getFrontend()->getValue($product),
                    'product_ids' => []
                ];
            }

            $options[$product->getData($attribute->getAttributeCode())]['product_ids'][] = $product->getId();
        }

        return $options;
    }

    /**
     * @return Product[]
     */
    public function getSimpleProducts()
    {
        return $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct());
    }

    /**
     * @return array
     */
    public function getOutOfStockSimpleProducts()
    {
        return array_filter($this->getSimpleProducts(), function ($product) {
            $websiteId = $this->getProduct()->getStore()->getWebsiteId();

            return !$this->stockItem->verifyStock($product->getId(), $websiteId) ||
                !$this->stockItem->getStockQty($product->getId(), $websiteId);
        });
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $isConfigurable  = $this->getProduct()->getTypeId() == Configurable::TYPE_CODE;
        $isModuleEnabled = $this->moduleHelper->isEnabled();

        if (!$isConfigurable || !$isModuleEnabled) {
            return '';
        }

        if (!count($this->getOutOfStockSimpleProducts())) {
            return '';
        }

        return parent::_toHtml();
    }
}