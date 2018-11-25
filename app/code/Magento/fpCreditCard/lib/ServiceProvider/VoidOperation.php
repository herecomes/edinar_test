<?php
namespace ServiceProvider;

class VoidOperation extends ChildTransaction {

	 protected function _buildRequestMessage() {	 	
    
   	 $request = array(
   	 	"transactionType"=> $this->getTransactionType()
   	 );
    	 return $request;
  	}
}
?>
