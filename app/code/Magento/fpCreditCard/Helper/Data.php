<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\fpCreditCard\Helper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

use Magento\fpCreditCard\Model\ServiceProvider;

/**
 * Fluid Pay Data Helper
 *
 * @api
 * @since 100.0.2
 */
class Data extends AbstractHelper
{
    /* Service Provider Pay * /

     /**
     * @var \Magento\fpCreditCard\Model\ServiceProvider;
     */
      protected $serviceprovider;

    /* End Here * /

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param  \Magento\fpCreditCard\Model\ServiceProvider;
     */
    public function __construct(
        ServiceProvider $serviceprovider
    ) {
        /* Payment Object */
         $this->serviceprovider = $serviceprovider;
        /* End Here */
        
    }

     /*   Payment Action Gateways      */
    public function updateOrderStatus(\Magento\Framework\Event\Observer $observer)
    {
          if($this->serviceprovider->setEventActionName())
          {
               $orderDetailStatus =  $this->serviceprovider->updateAllOrderDetailsTransactionStatus($observer);
          }
    }

    public function saveOrderPlaceData(\Magento\Framework\Event\Observer $observer)
    {
        $saveOrderStatus =  $this->serviceprovider->saveOrderPlaceDataTransactionStatus($observer);
         
    }

    /* Payment End */

    
}
