<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\fpCreditCard\Model;

use Magento\Framework\Exception\LocalizedException;


use Magento\fpCreditCard\Model\ServiceProvider;


/* Service Provider Gateway Libraries Files */

require_once  __DIR__. '/../lib/ServiceProvider.php';
require_once  __DIR__ . '/../lib/config/payment_config.php';

/* End Here */ 

/**
 * Class TransactionService
 * @package Magento\fpCreditCard\Model
 */
class TransactionService
{
    /**
     * Transaction Details gateway url
     */
    const CGI_URL_TD = 'https://apitest.authorize.net/xml/v1/request.api';

    const PAYMENT_UPDATE_STATUS_CODE_SUCCESS = 'Ok';

    const CONNECTION_TIMEOUT = 45;

    /**
     * Stored information about transaction
     *
     * @responseFactoryar array
     */
    protected $transactionDetails = [];

    /**
     * @var \Magento\Framework\Xml\Security
     */
    protected $xmlSecurityHelper;

    /**
     * @var \Magento\Payment\Model\Method\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $debugReplacePrivateDataKeys = ['merchantAuthentication', 'x_login'];

    /**
     * @param Security $xmlSecurityHelper
     * @param Logger $logger
     * @param ZendClientFactory $httpClientFactory
     */

     /* Service Provider Pay */ 
    protected $_encryptor;

    public $payment_action;
    public $payment_action_name;

    public $orderId;
    public $currency;
    
    public $transaction_id;
    public $transaction_amount;
    public $transaction_tax_amount;
    public $transaction_shipping_amount;
    public $transaction_status;
    public $message;
    public $paymentStatus;


    public $transactionAction;



    public function __construct(
      \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
               /* Service Provider Pay */ 
         $this->_encryptor = $encryptor;
    }


    /*     Service Provider Gateway Action    */

    public function sandbox_on_off_status($test_mode,$sandboxapiKey,$apiKey)
    {
        switch ($test_mode) {
            case 0:
                   \ServiceProvider\Settings::$apiKey =  $this->_encryptor->decrypt($apiKey);
                 \ServiceProvider\Settings::$apiBaseUrl = \ServiceProvider\Settings::$apiBaseUrl;
                   break;
            case 1:
                 \ServiceProvider\Settings::$sandboxUrl = \ServiceProvider\Settings::$sandboxUrl;
                 \ServiceProvider\Settings::$apiKey =  $this->_encryptor->decrypt($sandboxapiKey);
                  break;           
            default:
                return false;
        }

         $apiKey = \ServiceProvider\Settings::$apiKey;
         if($apiKey =='' || $apiKey ==null){
             return false;
         }
        return true;
    }

   public function getPaymentTransactionStatus($transaction_id)
   {
     // Check Status and make void/refund
      $transaction = new \ServiceProvider\QueryByTransactionId;
      $transaction->setTransactionID($transaction_id);
      $transaction->setTransactionType('get_transaction_status');
      $response = $transaction->submit();

      if ($response->isSuccess()) {
          return $response->getOrderStatus();
      }
      else if($response->isFailed()){        
         throw new \Exception($response->getMessage()); 
      }
      else{
        throw new \Exception('Something went wrong with processing the transaction.'); 
      }
   }

   public function setVoidTransaction($transaction_id)
   {     
    $void = new \ServiceProvider\VoidOperation;        
    $void->setTransactionType('void');
    $void->setTransactionID($transaction_id);

    $void_response = $void->submit();
        if ($void_response->isSuccess()) {
             $message = $void_response->getMessage();;
        }
        else if ($void_response->isFailed()) { 
         // Throw Error
           $message = $void_response->getStatus();          
         }        
      else{
            // Throw Error
             $message = 'Something went wrong with processing the transaction.';
        }

        return $message;
   }

   public function setCaptureTransaction($transaction_id,$payment,$tax_amount,$shipping_amount)
   {     
        $capture = new \ServiceProvider\CaptureOperation;     
        $capture->setTransactionType('capture');

        $capture->setTransactionID($transaction_id);
        $capture->money->setCents($payment);
        $capture->money->setTaxAmount($tax_amount); 
        $capture->money->setShippingAmount($shipping_amount);
        $capture->money->addShippingToAmount();

        $capture_response = $capture->submit();
        if ($capture_response->isSuccess()) {
             $message = $capture_response->getMessage();;
        }
        else if ($capture_response->isFailed()) { 
         // Throw Error
           $message = $capture_response->getStatus();          
         }        
      else{
            // Throw Error
             $message = 'Something went wrong with processing the transaction.';
        }

        return $message;
   }

   public function setRefundTransaction($transaction_id,$payment)
   {     
        $refund = new \ServiceProvider\RefundOperation;      
        $refund->setTransactionType('refund');
        
        $refund->setTransactionID($transaction_id);
        $refund->money->setCents($payment);          
        
        $refund_response = $refund->submit();
        if ($refund_response->isSuccess()) {
             $message = $refund_response->getMessage();;
        }
        else if ($refund_response->isFailed()) { 
         // Throw Error
           $message = $refund_response->getStatus();          
         }        
      else{
            // Throw Error
             $message = 'Something went wrong with processing the transaction.';
        }

        return $message;
   }


   public function setPlaceOrderTransaction(\Magento\Sales\Model\Order $order)
   {     
        $transaction = $this->buildRequest($order);
        $this->transaction_amount = $transaction->money->getOrderAmount();
        $this->transaction_tax_amount = $transaction->money->getTaxAmount();
        $this->transaction_shipping_amount = $transaction->money->getShippingAmount();

        $response = $transaction->submit();
        if ($response->isSuccess()) {
            $this->transaction_id = $response->getTrackingId();
            $this->transaction_status = $response->getOrderStatus();
            $this->paymentStatus = $response->getStatus();
            $this->message = $response->getMessage();
        }
        else if ($response->isFailed()) { 
            $this->message = $response->getMessage(); 
            $this->paymentStatus = $response->getStatus();         
        }        
        else{ 
            $this->message       = 'Something went wrong with processing the transaction.';
            $this->paymentStatus = 'No response received from API';
        }
        return $this->paymentStatus;
   }

    protected function buildRequest(\Magento\Sales\Model\Order $order)
    {
        if (!empty($order)){
            $this->transactionAction = 'OK';            
            if($this->payment_action == 'authorize_capture'){
                 $transaction = new \ServiceProvider\PaymentOperation;
                 $transaction->setTransactionType('payment');            
                 $this->payment_action_name = 'Capture';
            }
            else{
                  $transaction = new \ServiceProvider\AuthorizationOperation;
                  $transaction->setTransactionType('authorization');
                  $this->payment_action_name = 'Authorize';
            }

            $order_detail =  $order->getData();
            $payment =  $order->getPayment()->getData();
            $billing =  $order->getBillingAddress()->getData();
            $shipping =  $order->getShippingAddress()->getData();

            if (!empty($payment)) { 
                $card  = $payment['additional_information'];


                $amount = $order_detail['base_subtotal'];
                $currency = $order_detail['base_currency_code'];
                $tax =     $order_detail['base_tax_amount'];
                $shipping_amount = $payment['base_shipping_amount'];
                $amount = $amount +  $tax +  $shipping_amount;
                $description = $order_detail['shipping_description'];
                $orderId =   $payment['parent_id'];
                $po_number = $payment['po_number'];
                $card_number = @$payment['additional_information']['card_number'];
                $card_exp_month = @$payment['additional_information']['card_exp_month'];
                $card_exp_year = @$payment['additional_information']['card_exp_year'];
                $card_cvv =    @$payment['additional_information']['card_cvv'];

                $this->orderId = $orderId;
                $this->currency = $currency;

                $transaction->money->setCurrency($currency);
                $transaction->money->setAmount($amount);
                $transaction->money->setOrderAmount($transaction->money->getCents());
                $transaction->money->setAmount($tax);
                $transaction->money->setTaxAmount($transaction->money->getCents());
                $transaction->money->setAmount($shipping_amount);
                $transaction->money->setShippingAmount($transaction->money->getCents());
                $transaction->money->addShippingToAmount();

                $transaction->setDescription($description);
                $transaction->setOrderID($orderId);
                $transaction->setPoNumber($po_number);

                $transaction->card->setEntryType('keyed');
                $transaction->card->setCardNumber($card_number);
                $transaction->card->setCardHolder('Card Holder');
                $transaction->card->setCardExpMonth($card_exp_month);
                $transaction->card->setCardExpYear($card_exp_year);
                $transaction->card->setCardCvc($card_cvv);
                $transaction->card->setExpirationDate();
            }

            if (!empty($billing)) {            
                 $transaction->customer->setFirstName($billing['firstname']);
                 $transaction->customer->setLastName($billing['lastname']);
                 $transaction->customer->setCompany($billing['company']);
                 $transaction->customer->setAddress($billing['street']);
                 $transaction->customer->setCity($billing['city']);
                 $transaction->customer->setState(substr($billing['region'], 0,2));
                 $transaction->customer->setZip($billing['postcode']);
                 $transaction->customer->setCountry($billing['country_id']);
                 $transaction->customer->setPhone($billing['telephone']);
                 $transaction->customer->setFax($billing['fax']);
                 $transaction->customer->setIp();
                 $transaction->customer->setEmail($billing['email']);
            }

           
            if (!empty($shipping)) {
                 $transaction->shipping_address->setFirstName($shipping['firstname']);
                 $transaction->shipping_address->setLastName($shipping['lastname']);
                 $transaction->shipping_address->setCompany($shipping['company']);
                 $transaction->shipping_address->setAddress($shipping['street']);
                 $transaction->shipping_address->setCity(substr($shipping['region'], 0,2));
                 $transaction->shipping_address->setState($shipping['region']);
                 $transaction->shipping_address->setZip($shipping['postcode']);
                 $transaction->shipping_address->setCountry($shipping['country_id']);
                 $transaction->shipping_address->setPhone($shipping['telephone']);
                 $transaction->shipping_address->setFax($shipping['fax']);
                 $transaction->shipping_address->setEmail($shipping['email']);
            }
            return $transaction;
        }        
    }
}
