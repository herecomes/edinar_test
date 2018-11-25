<?php
namespace ServiceProvider;

abstract class ApiAbstract {
  protected abstract function _buildRequestMessage();
  protected $_order_id;
  protected $_po_number;
  protected $_language;
  protected $_endpoint;
  protected $_transactionType;
  protected $_transactionID;
 
  public function submit() {
    try {
      $response = $this->_remoteRequest();
    } catch (\Exception $e) {
      $msg = $e->getMessage();
      $response = '{ "errors":"' . $msg . '", "message":"' . $msg . '" }';
    }
    return new Response($response);
  }

  protected function _remoteRequest() {
     return GatewayTransport::submit( Settings::$apiBaseUrl,Settings::$apiKey,$this->_endpoint(), $this->_buildRequestMessage(),$this->getTransactionType());
  }

  protected function _endpoint() {
      if($this->getTransactionType()){
          switch ($this->getTransactionType()) {
          case 'payment':
              $this->_endpoint = Settings::$apiBaseUrl . 'transaction';
              break;
          case 'void':
              $this->_endpoint = Settings::$apiBaseUrl . 'transaction/'.$this->getTransactionID().'/void';
              break;  
          case 'refund':
               $this->_endpoint = Settings::$apiBaseUrl . 'transaction/'.$this->getTransactionID().'/refund';
              break;
          case 'capture':
              $this->_endpoint = Settings::$apiBaseUrl . 'transaction/'.$this->getTransactionID().'/capture';
              break;
          case 'authorization':
              $this->_endpoint = Settings::$apiBaseUrl . 'transaction';
              break;
          case 'get_transaction_status':
              $this->_endpoint = Settings::$apiBaseUrl . 'transaction/'.$this->getTransactionID();
              break;
          case 'query_transactions':
              $this->_endpoint = Settings::$apiBaseUrl . 'transaction/search';
              break;
          default:
              $this->_endpoint = Settings::$apiBaseUrl . 'transaction';             
            break;
        }
      }
      
    return $this->_endpoint;
  }


public function setOrderID($order_id) {
    $this->_order_id = $order_id;
  }

  public function getOrderID() {
    return $this->_order_id;
  }

public function setPoNumber($po_number) {
    $this->_po_number = $po_number;
  }

  public function getPoNumber() {
    return $this->_po_number;
  }

  public function setTransactionID($transactionId) {
    $this->_transactionID = $transactionId;
  }

  public function getTransactionID() {
    return $this->_transactionID;
  }

  public function setTransactionType($transactionType) {
    $this->_transactionType = $transactionType;
  }

  public function getTransactionType() {
    return $this->_transactionType;
  }



  protected function _getTransactionType() {
    list($module,$klass) = explode('\\', get_class($this));
    $klass = str_replace('Operation', '', $klass);
    $klass = strtolower($klass) . 's';
    return $klass;
  }
  public function setLanguage($language_code) {
    if (in_array($language_code, Language::getSupportedLanguages())) {
      $this->_language = $language_code;
    }else{
      $this->_language = Language::getDefaultLanguage();
    }
  }

  public function getLanguage() {
    return $this->_language;
  }
}
?>
