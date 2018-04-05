<?php

namespace JustCoded\BackInStockConfigurable\Api;

/**
 * @api
 */
interface SubscriptionInterface
{
    /**
     * @param string $email
     * @param string $simple
     * @param string $configurable
     *
     * @return array
     */
    public function subscribe(
        $email,
        $simple,
        $configurable
    );
}