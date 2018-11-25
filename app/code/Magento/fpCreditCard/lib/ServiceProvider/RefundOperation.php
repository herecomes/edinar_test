<?php
namespace ServiceProvider;

class RefundOperation extends ChildTransaction {
  protected $_reason;
 

  public function setReason($reason) {
    $this->_reason = $reason;
  }

  public function getReason() {
    return $this->_reason;
  }

/*  protected function _buildRequestMessage() {
    $request = parent::_buildRequestMessage();

    $request['request']['reason'] = $this->getReason();

    return $request;
  }*/

    protected function _buildRequestMessage() {    

     $request = array(
        "transactionType"=> $this->getTransactionType(),
         /*=====================================================*/       
         "amount"=> (int)$this->money->getCents(),
         /*=====================================================*/
     );

       return $request;
    }
}
?>
