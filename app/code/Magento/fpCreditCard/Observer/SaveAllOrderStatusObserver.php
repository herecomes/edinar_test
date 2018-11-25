<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\fpCreditCard\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SaveAllOrderStatusObserver implements ObserverInterface
{
    /**
     *
     * @var \Magento\fpCreditCard\Helper\Data
     */
    protected $servicePayData;
    protected $resultJsonFactory; 
    protected $jsonHelper; 

    /*===========*/
    protected $_responseFactory;
     protected $_url;

    /**
     * @param \Magento\fpCreditCard\Helper\Data $servicePayData
     */
    public function __construct(
        \Magento\fpCreditCard\Helper\Data $servicePayData, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,\Magento\Framework\Json\Helper\Data $jsonHelper,\Magento\Framework\App\ResponseFactory $responseFactory,\Magento\Framework\UrlInterface $url
    ) {
         $this->servicePayData = $servicePayData;

          $this->resultJsonFactory = $resultJsonFactory;
          $this->jsonHelper = $jsonHelper;
        
          /*=======*/
           $this->_responseFactory = $responseFactory;
        $this->_url = $url;
    }

    /**
     * Save place order into transaction table.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
         /* @var $order Order */   
         $save  = $this->servicePayData->saveOrderPlaceData($observer);
         return $save;
     }
}
