<?php

namespace JustCoded\BackInStockConfigurable\Cron;

use JustCoded\BackInStockConfigurable\Helper\Data;
use JustCoded\BackInStockConfigurable\Model\ResourceModel\Subscription\CollectionFactory;
use JustCoded\BackInStockConfigurable\Model\Subscription;
use JustCoded\BackInStockConfigurable\Model\SubscriptionRepository;
use Psr\Log\LoggerInterface;

class Email
{
    const LOG_PREFIX = 'IN_STOCK_NOTIFY: ';

    /**
     * @var Data
     */
    private $moduleHelper;

    /**
     * @var CollectionFactory
     */
    private $subscriptionsFactory;

    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Data $moduleHelper,
        CollectionFactory $subscriptionsFactory,
        SubscriptionRepository $subscriptionRepository,
        LoggerInterface $logger
    ) {
        $this->moduleHelper           = $moduleHelper;
        $this->subscriptionsFactory   = $subscriptionsFactory;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger                 = $logger;
    }

    /**
     * @param Subscription $subscription
     * @return string
     */
    private function getSubscriptionInfo(Subscription $subscription)
    {
        return 'subscription for email: ' . $subscription->getEmail() .
            ', product_id: ' . $subscription->getProductId();
    }

    /**
     * @param $msg
     * @return string
     */
    private function formatMessage($msg)
    {
        return self::LOG_PREFIX . $msg;
    }

    /**
     * @param $msg
     */
    private function info($msg)
    {
        $this->logger->info($this->formatMessage($msg));
    }

    /**
     * @param $msg
     */
    private function error($msg)
    {
        $this->logger->error($this->formatMessage($msg));
    }

    /**
     * Send emails with subscriptions
     */
    public function execute()
    {
        $this->info('Check cron mailer');

        if (!$this->moduleHelper->isScheduledNotifications()) {
            return;
        }

        $this->info('Start mailing');

        $subscriptions = $this->subscriptionsFactory->create();

        foreach ($subscriptions as $subscription) {
            $this->info('Check ' . $this->getSubscriptionInfo($subscription));
            if ($subscription->isReady()) {
                $this->info('Ready for process ' . $this->getSubscriptionInfo($subscription));
                try {
                    $subscription->proceed();

                    $this->info('Was send ' . $this->getSubscriptionInfo($subscription));

                    $this->subscriptionRepository->delete($subscription);
                } catch (\Exception $e) {
                    $this->error(
                        'Error for ' . $this->getSubscriptionInfo($subscription) .
                        'Exception: ' . $e->getMessage()
                    );
                }
            } else {
                $this->info('Not ready for process ' . $this->getSubscriptionInfo($subscription));
            }
        }

        $this->info('Stop mailing');
    }
}