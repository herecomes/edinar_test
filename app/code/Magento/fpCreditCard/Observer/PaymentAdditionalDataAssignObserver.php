<?php
namespace Magento\fpCreditCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Payment\Observer\AbstractDataAssignObserver;

class PaymentAdditionalDataAssignObserver extends AbstractDataAssignObserver
{
    const card_number_index = 'card_number';
    const card_exp_month_index = 'card_exp_month';
    const card_exp_year_index = 'card_exp_year';
    const card_cvv_index = 'card_cvv';
    const card_type_index = 'card_type';

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        
        
        $paymentInfo = $this->readPaymentModelArgument($observer);
        $paymentInfo->setAdditionalInformation(
            self::card_number_index,
            $additionalData[self::card_number_index]
        );

        $paymentInfo->setAdditionalInformation(
            self::card_exp_month_index,
            $additionalData[self::card_exp_month_index]
        );

        $paymentInfo->setAdditionalInformation(
            self::card_exp_year_index,
            $additionalData[self::card_exp_year_index]
        );

        $paymentInfo->setAdditionalInformation(
            self::card_cvv_index,
            $additionalData[self::card_cvv_index]
        );

        $paymentInfo->setAdditionalInformation(
            self::card_type_index,
            $additionalData[self::card_type_index]
        );



    }
}