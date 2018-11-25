<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\fpCreditCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class DataAssignObserver extends AbstractDataAssignObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);

        $paymentInfo = $method->getInfoInstance();

        
        $data = $data->getData('additional_data');

        if(isset($data['card_number'])){

        $paymentInfo->setAdditionalInformation(
                'card_number',
                $data['card_number']
            );
          }
       


         if(isset($data['card_exp_month'])){
            $paymentInfo->setAdditionalInformation(
                'card_exp_month',
                $data['card_exp_month']
            );
        }
        

         if(isset($data['card_exp_year'])){
            $paymentInfo->setAdditionalInformation(
                'card_exp_year',
                $data['card_exp_year']
            );
      }

         if(isset($data['card_cvv'])){
            $paymentInfo->setAdditionalInformation(
                'card_cvv',
                $data['card_cvv']
            );
        }
        

        
            $paymentInfo->setAdditionalInformation(
                'card_type',
                $data['card_type']
            );
        
    }
}
