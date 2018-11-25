<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\fpCreditCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class UpdateAllOrderStatusObserver implements ObserverInterface
{
    /**
     *
     * @var \Magento\fpCreditCard\Helper\Data
     */
    protected $servicePayData;

    /**
     * @param \Magento\fpCreditCard\Helper\Data $servicePayData
     */
    public function __construct(
        \Magento\fpCreditCard\Helper\Data $servicePayData
    ) {
        $this->servicePayData = $servicePayData;
    }

    /**
     * Save order into registry to use it in the overloaded controller.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order Order */
              $this->servicePayData->updateOrderStatus($observer);
             
    }
}
