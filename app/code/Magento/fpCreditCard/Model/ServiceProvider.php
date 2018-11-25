<?php namespace Magento\fpCreditCard\Model;
use Magento\fpCreditCard\Model\TransactionService;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class ServiceProvider extends \Magento\Payment\Model\Method\Cc
{
   /* Service Provider */ 

    /**
     * @var \Magento\fpCreditCard\Model\TransactionService;
     */

    protected $resultJsonFactory;
   protected $response;
   protected $jsonHelper; 


     protected $_order;
     protected $transactionService;

     protected $scopeConfig;

      protected $transaction_status;
      protected $transaction_id;
      protected $payment;

      protected $tax_amount;
      protected $shipping_amount;

      protected $order_id;
      protected $count;

     protected $request;

     protected $_transactionBuilder;

     protected $controllerName;
     protected $actionName;

     protected $connection;

     protected $messageManager;
     protected $orderManagement;

     
    const ACTION_TYPE_PLACEORDER = 'PLACEORDER';    
    const ACTION_TYPE_INVOICE_SAVE = 'SAVE';
    const ACTION_TYPE_VOID = 'VOIDPAYMENT';
    const ACTION_TYPE_CANCEL = 'CANCEL';
    const ACTION_TYPE_REFUND = 'REFUND';

    const SANDBOX_API_KEY = 'payment/fpCreditCard/sandbox_gateway_key';
    const IS_SANDBOX = 'payment/fpCreditCard/is_sandbox';
    const API_KEY = 'payment/fpCreditCard/merchant_gateway_key';
    const PAYMENT_ACTION = 'payment/fpCreditCard/payment_action';

    const TABLE_NAME = 'fpCreditCard_transaction';

    /* End */ 


    public function __construct(
                \Magento\Framework\Model\Context $context,\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,\Magento\Framework\App\Request\Http $request,\Magento\Sales\Model\Order\Payment\Transaction\Builder $transactionBuilder,
        TransactionService $transactionService,\Magento\Framework\App\Action\Context $context1,
    \Magento\Sales\Api\OrderManagementInterface $orderManagement,\Magento\Framework\Message\ManagerInterface $messageManager,\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,\Magento\Framework\App\ResponseFactory $responseFactory,\Magento\Framework\App\Response\Http $response,\Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Framework\App\Helper\Context $contextHelper
    ) {

        /* Service Provider */ 
         

        $this->scopeConfig = $scopeConfig;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->request = $request;
         $this->_transactionBuilder =  $transactionBuilder;
          $this->transactionService = $transactionService;

           $this->orderManagement = $orderManagement;
            $this->messageManager = $messageManager;
         
         

          $this->resultJsonFactory = $resultJsonFactory;
          $this->responseFactory = $responseFactory;
          $this->response = $response;
         $this->jsonHelper = $jsonHelper;
          
    }

  /*     Service Provider Gateway Action    */
     /**
     * get and set Event Controller Name and action 
     *
     *
     * @return bool
     */
   
    public function setEventActionName()
    {
        $requestInterface = $this->objectManager->get('Magento\Framework\App\RequestInterface');

         $this->controllerName  = strtoupper($requestInterface->getControllerName());
          
          /*==========*/
         
         if($this->controllerName == '' && $requestInterface->getActionName() == ''){
             $this->actionName     = strtoupper('placeorder');              
         }
         else if($this->controllerName == 'ORDER_CREDITMEMO'){
             $this->actionName     = strtoupper('refund');
         }
         else{
                $this->actionName     = strtoupper($requestInterface->getActionName());
         }
        
        if(!$this->actionName){
            return false;
        }
        return true;
    }

    /**
     *  post request and return response
     *
     * @param (\Magento\Framework\Event\Observer $observer
     */

    public function updateAllOrderDetailsTransactionStatus(\Magento\Framework\Event\Observer $observer)
    {

        switch ($this->actionName) {
            case ServiceProvider::ACTION_TYPE_CANCEL:
                     $order = $observer->getEvent()->getOrder();
                     $this->order_id = $order->getId();
                     /*call cancel order function */
                     $transactionMethod = 'cancel';                     
                     break;
            case ServiceProvider::ACTION_TYPE_VOID:
                  /*call void function*/
                      $params = $this->request->getParams();
                     $this->order_id = $params['order_id'];
                      $transactionMethod = 'void'; 
                  break;
            case ServiceProvider::ACTION_TYPE_INVOICE_SAVE:
                 /*call capture function*/
                  $invoice = $observer->getEvent()->getInvoice();
                  $this->order_id = $invoice->getOrderId();
                  if($this->order_id =='' || $this->order_id == null){
                    return true;
                  }
                  $transactionMethod = 'capture'; 
                 break;
            case ServiceProvider::ACTION_TYPE_REFUND:
                /*call refund order function*/
                 $params = $this->request->getParams();
                 $this->order_id = $params['order_id'];
                 $transactionMethod = 'refund'; 
                break;
            default:
                return false;
        }

       

        $orderStatus = $this->getOrderDetails($this->order_id);        
        if(!$orderStatus){
             return true;
        }
        else{
             if($this->transaction_id !='' && $this->transaction_id !=NULL)
             {
                $transactionStatus = $this->transactionService->getPaymentTransactionStatus($this->transaction_id);
                if($transactionStatus !=''){

              $this->transaction_status = $transactionStatus;
              $callAPI = $this->callApiTransaction($transactionMethod,$this->transaction_status);
                 if(!$callAPI){
                        throw new \Exception('Something went wrong with processing the transaction. API Transaction Error Found');
                 }
             }
              else{
                  throw new \Exception('Something went wrong with processing the transaction. Transaction is not Found');
              }
              return true;
          }  
        }    
    }

    public function saveOrderPlaceDataTransactionStatus(\Magento\Framework\Event\Observer $observer)
    {      
       if($this->getPaymenMethodConfig()){
            if($this->getConnection() !=''){
                $tableName = $this->getConnection();
                     $order = $observer->getEvent()->getOrder();
                     $order_id = $order->getIncrementId();

                     $order = $this->objectManager->get('Magento\Sales\Model\Order');
                     $order_information = $order->loadByIncrementId($order_id);
                    
                     $placeOrderTransaction = $this->transactionService->setPlaceOrderTransaction($order_information);

                 if($placeOrderTransaction =='success'){

                    
                         $orderId  =$this->transactionService->orderId;
                         $message = $this->transactionService->message;

                         $transaction_status = $this->transactionService->transaction_status;
   
                         if($transaction_status =='declined')
                         {
                            $payment_action_name = 'Void';
                        }
                        else{
                                $payment_action_name = $this->transactionService->payment_action_name;
                        }

                         $transaction_id = $this->transactionService->transaction_id;


                         $transaction_order_id = $this->updateTransactionMessage($tableName,$orderId,$transaction_status,$message,$transaction_id,$payment_action_name);

                        
                          if($this->transactionService->transaction_status =='declined')
                             {
                               // $this->messageManager->addError($this->transactionService->paymentStatus);
                                 $orderDelete = $this->putErrorMessage($observer);
                                   if($orderDelete){
                                        return $this->redirectToPage();
                                     }
                             }
                        


                        if(empty($transaction_order_id)){
                              /*  ADD void or refund condition */
                                if($this->transactionService->payment_action == 'authorize'){
                                        $cancelVoidTransaction = $this->transactionService->setVoidTransaction($this->transactionService->transaction_id);
                                        if($cancelTransaction =='success'){
                                                $this->messageManager->addError('Your Order is  not complet');
                                                $this->messageManager->addError('Transaction is not completed');
                                        }
                                }
                                else if($this->transactionService->payment_action == 'authorize_capture'){
                                     $this->transaction_id =   $transaction_id;
                                     $this->transaction_status =    $transaction_status;

                                      $this->payment =  $this->transactionService->transaction_amount;

                                      $this->order_id = $orderId;

                                       $cancelRefundTransaction = $this->callRefundTransaction();

                                    if($cancelRefundTransaction){
                                            $this->messageManager->addError('Transaction is not completed');
                                            $this->messageManager->addError('Your Order is  not completed');
                                        }
                                }                               
                        }
                 }
                 else if($placeOrderTransaction =='failure'){
                     // Make Order Cancel 
                    $txt = json_encode($placeOrderTransaction);

                    $myfile = fopen("debugplaceOrderTransaction.txt", "a+") or die("Unable to open file!");
                    fwrite($myfile, $txt);
                    fclose($myfile);

                     $this->messageManager->addError($this->transactionService->paymentStatus);
                     $orderDelete =  $this->putErrorMessage($observer);

                     if($orderDelete){
                        return $this->redirectToPage();
                     }

                        
                 }
                 else{
                      // Make Order Cancel 
                     $this->messageManager->addError($this->transactionService->message);
                     return $this->putErrorMessage($observer);
                    
                 }
            }
            else{
                  // Make Order Cancel 
                $this->messageManager->addError('Payment transaction connection error');
                return $this->putErrorMessage($observer);
               
            }
        }
        else{
               // Make Order Cancel 
             $this->messageManager->addError('Payment method configuration error');
             return $this->putErrorMessage($observer);
             
        }

        return true;
  
    }


    public function getPaymenMethodConfig(){
      $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
      $sandboxapiKey = $this->scopeConfig->getValue(ServiceProvider::SANDBOX_API_KEY, $storeScope);
      $test_mode = $this->scopeConfig->getValue(ServiceProvider::IS_SANDBOX, $storeScope);
      $apiKey = $this->scopeConfig->getValue(ServiceProvider::API_KEY, $storeScope);
      $payment_action = $this->scopeConfig->getValue(ServiceProvider::PAYMENT_ACTION, $storeScope);

      $this->transactionService->payment_action = $payment_action;

      $sandboxStatus = $this->transactionService->sandbox_on_off_status($test_mode,$sandboxapiKey,$apiKey);
      if(!$sandboxStatus){
           // Add error message Exception
             throw new \Exception('Something went wrong with processing the transaction. Payment method configuration error');
      }
      return $sandboxStatus;
    }

  public function getConnection(){
      $resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
      $this->connection = $resource->getConnection();
      $tableName = $resource->getTableName(ServiceProvider::TABLE_NAME); //gives table name with prefix

      return $tableName;
  }

  public function getOrderData($orderId,$tableName){
      $this->order_id = $orderId;
      $sql = "Select * FROM " . $tableName." Where order_id = '".$this->order_id."' LIMIT 1";
      $result = $this->connection->fetchAll($sql); // gives associated array, table fields as key in array.

     $this->count = count($result);
     if($this->count > 0 ){
        foreach ($result as $transaction) {
            $this->transaction_status   = $transaction['transaction_status'];
            $this->transaction_id       = $transaction['transaction_id'];
            $this->order_id             = $transaction['order_id'];
            $this->payment              = $transaction['transaction_amount'];
            $this->tax_amount           = $transaction['transaction_tax_amount'];
            $this->shipping_amount      = $transaction['transaction_shipping_amount'];

        }

        return true;       
     }

     return false;    
   
  }

    public function getOrderDetails($orderId){
        
        if($this->getPaymenMethodConfig()){
            if($this->getConnection() !=''){
                $tableName = $this->getConnection();
                $orderDetaisStatus = $this->getOrderData($orderId,$tableName);
                 return $orderDetaisStatus;
            }
        }
    }

   public function callApiTransaction($transactionMethod,$transaction_status) {
         switch ($transactionMethod) {
            case 'cancel':  
                     /*call void function*/ 
                     $apiStatus = $this->callVoidTransaction();                                   
                     break;
            case 'void':
                  /*call void function*/
                      $apiStatus = $this->callVoidTransaction();                                   
                     break;
                  break;
            case 'capture':
                 /*call capture function*/
                     $apiStatus = $this->callCaptureTransaction();  
                 break;
            case 'refund':
                /*call refund order function*/     
                    $apiStatus = $this->callRefundTransaction();          
                break;
            default:
                return false;
        }
         return $apiStatus;
    }

    
    public function callVoidTransaction(){
        if($this->getConnection() !=''){
        $tableName = $this->getConnection();
        $message = 'success';
        $transaction_status = 'Canceled/Void';
        $payment_action_name = 'Void';
          if($this->transaction_status != 'voided'){
                $voidTransaction = $this->transactionService->setVoidTransaction($this->transaction_id);
                 if($voidTransaction =='success'){
                         $message = $voidTransaction;
                        $transaction_message = $this->updateTransactionMessage($tableName,$this->order_id,$transaction_status,$message,$this->transaction_id,$payment_action_name);
                        if(!$transaction_message){
                               throw new \Exception('Something went wrong with processing the transaction. Transaction Error Found');
                        }
                 }
                 else if($voidTransaction =='failure'){
                     throw new \Exception($voidTransaction);
                 }
                 else{
                         throw new \Exception($voidTransaction);
                 }
         }
         else{
                $transaction_message = $this->updateTransactionMessage($tableName,$this->order_id,$transaction_status,$message,$this->transaction_id,$payment_action_name);
                if(!$transaction_message){
                       throw new \Exception('Something went wrong with processing the transaction. Transaction Error Found');
                }
         }
      }
    else{
          throw new \Exception('Something went wrong with processing the transaction.');
        }
        return true;
    }

    public function callCaptureTransaction(){
        if($this->getConnection() !=''){
        $tableName = $this->getConnection();
          if($this->transaction_status == 'authorized'){
                $captureTransaction = $this->transactionService->setCaptureTransaction($this->transaction_id,$this->payment,$this->tax_amount,$this->shipping_amount);

                 if($captureTransaction =='success'){
                         $message = $captureTransaction;
                         $transaction_status = 'Captured';
                         $payment_action_name = 'Capture';
                         $transaction_message = $this->updateTransactionMessage($tableName,$this->order_id,$transaction_status,$message,$this->transaction_id,$payment_action_name);
                        if(!$transaction_message){
                               throw new \Exception('Something went wrong with processing the transaction. Transaction Error Found');
                        }
                 }
                 else if($captureTransaction =='failure'){
                     throw new \Exception($captureTransaction);
                 }
                 else{
                         throw new \Exception($captureTransaction);
                 }
         }
      }
    else{
          throw new \Exception('Something went wrong with processing the transaction. Transaction table is not found');
        }
        return true;
    }

    public function callRefundTransaction(){
        if($this->getConnection() !=''){
        $tableName = $this->getConnection();
        
          if($this->transaction_status == 'pending_settlement' || $this->transaction_status == 'settled'){
                    
                    if($this->transaction_status == 'pending_settlement'){
                         $refundTransaction = $this->transactionService->setVoidTransaction($this->transaction_id);
                          $transaction_status = 'Voided/Refunded';
                          $payment_action_name = 'Refund';
                    }
                    else{
                        $refundTransaction = $this->transactionService->setRefundTransaction($this->transaction_id,$this->payment);

                         $transaction_status = 'Refunded';
                         $payment_action_name = 'Refund';
                    }
                      

                 if($refundTransaction =='success'){
                         $message = $refundTransaction;
                        
                         $transaction_message = $this->updateTransactionMessage($tableName,$this->order_id,$transaction_status,$message,$this->transaction_id,$payment_action_name);
                        if(!$transaction_message){
                               throw new \Exception('Something went wrong with processing the transaction. Transaction Error Found');
                        }

                 }
                 else if($refundTransaction =='failure'){
                     throw new \Exception($refundTransaction);
                 }
                 else{
                         throw new \Exception($refundTransaction);
                 }
         }
      }
    else{
          throw new \Exception('Something went wrong with processing the transaction. Transaction table is not found');
        }
        return true;
    }



    public function updateTransactionMessage($tableName,$order_id,$transaction_status,$message,$transaction_id,$payment_action_name){
          if($this->transactionService->transactionAction == 'OK'){
             $sql = "INSERT " . $tableName . "
                   SET 
                    order_id                    =   '".$order_id."',
                    transaction_type            =   '".$payment_action_name."',
                    transaction_id              =   '".$transaction_id."',
                    transaction_status          =   '".$transaction_status."',
                    transaction_amount          =   '".$this->transactionService->transaction_amount."',
                    transaction_tax_amount      =   '".$this->transactionService->transaction_tax_amount."',
                    transaction_shipping_amount =   '".$this->transactionService->transaction_shipping_amount."',
                    currency                    =   '".$this->transactionService->currency."',
                    message                     =   '".$message."',
                    created_at                  =   '".date('Y-m-d H:i:s')."'";
          }
          else{

          $sql = "UPDATE " . $tableName . "
             SET
               transaction_status = '".$transaction_status."',
               message = '".$message."',
               updated_at = '".date('Y-m-d H:i:s')."' 
                WHERE
                     order_id = '".$order_id."'";
           }


            $this->connection->query($sql);

           // Add new Transaction of with Message

             $this->_order = $this->objectManager->create('Magento\Sales\Api\Data\OrderInterface')->load($order_id);
               
            try{
               $transStatus =   $this->addTransactionToOrder($this->_order,$transaction_id,$payment_action_name,$this->_transactionBuilder);  
                return $transStatus;
            }
           catch (\Exception $e) {
                 $this->messageManager->addError($e->getMessage());
            }
     } 


  public function addTransactionToOrder($order, $trans_id,$payment_action_name,$_transactionBuilder) {
            $payment = $order->getPayment();
            if($payment_action_name == 'Authorize'){
                $trans_id = $trans_id.'-Service Provider'; 
            }
            else{
                    $trans_id = $trans_id.'-Service Provider-'.$payment_action_name; 
            }


            $payment->setLastTransId($trans_id);
            $payment->setTransactionId($trans_id);
            $payment->setAdditionalInformation(
                  [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array)$payment->getAdditionalInformation()]
            );
            $formatedPrice = $order->getBaseCurrency()->formatTxt(
                            $order->getGrandTotal()
                        );

            $message = __('The '.$payment_action_name .' Amount is %1.', $formatedPrice);

             switch ($payment_action_name) {
                case "Capture":
                     $type = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE;
                    break;
                case "Refund":
                     $type = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_REFUND;
                    break;
                case "Void":
                     $type = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_VOID;
                    break;
                default:
                     $type = \Magento\Sales\Model\Order\Payment\Transaction::TYPE_AUTH;
             }

            //get the object of builder class Magento\Sales\Model\Order\Payment\Transaction\Builder
            $trans = $_transactionBuilder;
            $transaction = $trans->setPayment($payment)
            ->setOrder($order)
                        ->setTransactionId($trans_id)
                        ->setAdditionalInformation(
                            [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array)$payment->getAdditionalInformation()]
                        )
                        ->setFailSafe(true)
                        //build method creates the transaction and returns the object
                
                        ->build($type);
                       
                        $payment->addTransactionCommentsToOrder(
                            $transaction,
                            $message
                        );
                        $payment->setParentTransactionId(null);

                        $payment->save();
                        $order->save();
                        $transaction->save();
              return  $transaction->getTransactionId();
    }


    public function redirectToPage(){

        return true;
 /*       $this->messageManager->addError('Your order is not completed');
     $url = $this->objectManager->get(
                        \Magento\Backend\Model\UrlInterface::class
                    )->getUrl(
                        'checkout/cart/index'
                    );

     $this->responseFactory->create()
                    ->setRedirect($url)
                    ->sendResponse();
            exit(0);
            return $this;*/
    }
    

    public function cancelCurrentOrder($orderId){
            $get_order_id = $orderId;
            $order = $this->objectManager->create('Magento\Sales\Model\Order')->load($get_order_id);
            $orderdata  = $order->getData();
            $order_status = $orderdata["status"];

            if($order_status == "processing"){
                $this->orderManagement->cancel($get_order_id);  
                $this->messageManager->addError('Your Order is not complete. Please try again.');
            }
    }

    public function getCurrentOrderId(\Magento\Framework\Event\Observer $observer){
         $order = $observer->getEvent()->getOrder();
         $order_id = $order->getEntityId();
         return $order_id;
    }

    public function putErrorMessage(\Magento\Framework\Event\Observer $observer){
         $order_id = $this->getCurrentOrderId($observer);

         if (!empty($order_id)) { 
             $this->cancelCurrentOrder($order_id);
             return true;
         }
         return false;
    }


}
