<?php
namespace ServiceProvider;

class CaptureOperation extends ChildTransaction {

	  protected function _buildRequestMessage() {  
	     $request = array(
	       "transactionType"=> (int)$this->getTransactionType(),	
	       /*=====================================================*/       
	       "amount"=> $this->money->getCents(),
	       /*=====================================================*/
	       "tax_amount"=> (int)$this->money->getTaxAmount(),
	       "tax_exempt"=> false,
	       "shipping_amount"=> (int)$this->money->getShippingAmount()	       
	     );
               
           
	       return $request;
    }

}
?>
