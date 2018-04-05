<?php

namespace JustCoded\BackInStockConfigurable\Helper;

use JustCoded\BackInStockConfigurable\Model\Subscription;
use JustCoded\BackInStockConfigurable\Model\SubscriptionRepository;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface as State;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const EMAIL_TEMPLATE = 'back_in_stock_configurable_notification_email_template';

    const XML_PATH_IS_ENABLED                  = 'justcoded_back_in_stock_configurable/settings/enabled';
    const XML_PATH_IS_SCHEDULED                = 'justcoded_back_in_stock_configurable/settings/cron_send_notifications_enable';
    const XML_PATH_EMAIL_FROM                  = 'trans_email/ident_support/email';
    const XML_PATH_NAME_FROM                   = 'trans_email/ident_support/name';
    const XML_PATH_ATTRIBUTES_SORTING_ORDER    = 'justcoded_back_in_stock_configurable/settings/attributes_sorting_order';
    const XML_PATH_POPUP_HEADER_CMS_BLOCK_ID   = 'justcoded_back_in_stock_configurable/settings/popup_header_cms_block_id';

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var State
     */
    private $inlineTranslation;

    /**
     * @var CollectionFactory
     */
    private $templatesFactory;

    public function __construct(
        Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SubscriptionRepository $subscriptionRepository,
        TransportBuilder $transportBuilder,
        State $inlineTranslation,
        CollectionFactory $templatesFactory
    ) {
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->transportBuilder       = $transportBuilder;
        $this->inlineTranslation      = $inlineTranslation;
        $this->templatesFactory       = $templatesFactory;

        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return bool
     */
    public function isScheduledNotifications()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_SCHEDULED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param Subscription $subscription
     * @return array
     */
    protected function getEmailTemplateVariables(Subscription $subscription)
    {
        return [
            'product' => $subscription->getProduct()
        ];
    }

    /**
     * @return string
     */
    public function getEmailFrom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_FROM,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getNameFrom()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_NAME_FROM,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param Subscription $subscription
     */
    public function sendMail(Subscription $subscription)
    {
        $this->inlineTranslation->suspend();

        $template = $this->templatesFactory->create()->addFieldToFilter('template_code', self::EMAIL_TEMPLATE)->getFirstItem();

        $identifier = $template->getId() ? $template->getId() : self::EMAIL_TEMPLATE;

        $transport = $this->transportBuilder->setTemplateIdentifier($identifier)
            ->setTemplateOptions([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $subscription->getStoreId()
            ])->setTemplateVars($this->getEmailTemplateVariables($subscription)
            )->setFrom([
                'email' => $this->getEmailFrom(),
                'name'  => $this->getNameFrom(),
            ])->addTo($subscription->getEmail())
            ->getTransport();

        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }

    /**
     * @return array
     */
    public function getAttributesSortingOrder()
    {
        $rawValue = $this->scopeConfig->getValue(
            self::XML_PATH_ATTRIBUTES_SORTING_ORDER,
            ScopeInterface::SCOPE_STORE
        );

        return array_map('trim', explode(',', $rawValue));
    }

    /**
     * @return string
     */
    public function getPopupHeaderCmsBlockId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_POPUP_HEADER_CMS_BLOCK_ID,
            ScopeInterface::SCOPE_STORE
        );
    }
}

