<?php

namespace JustCoded\BackInStockConfigurable\Model\ResourceModel\Subscription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \JustCoded\BackInStockConfigurable\Model\Subscription::class,
            \JustCoded\BackInStockConfigurable\Model\ResourceModel\Subscription::class
        );
    }
}
