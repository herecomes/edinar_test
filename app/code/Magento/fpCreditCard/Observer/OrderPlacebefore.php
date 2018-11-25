<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\fpCreditCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Framework\Event\ObserverInterface;

class OrderPlacebefore implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   $order = $observer->getEvent()->getOrder();
        /*$txt = json_encode($order);
        $txt .= "its working correctly";
        $myfile = fopen("debugOrderSubmitAllEventDetails.txt", "a+") or die("Unable to open file!");
        fwrite($myfile, $txt);
        fclose($myfile);*/
    }   
}