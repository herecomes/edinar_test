<?php
namespace ServiceProvider;

class PaymentOperation extends AuthorizationOperation {
	
	protected function _buildRequestMessage() {	 	
    
    $request = array(			
					"transactionType"=> $this->getTransactionType(),
					"type"=> "sale",
					"amount"=> $this->money->getOrderAmount(),
					"tax_amount"=> $this->money->getTaxAmount(),
					"shipping_amount"=> $this->money->getShippingAmount(),
					"currency"=> $this->money->getCurrency(),

					"description"=> $this->getDescription(),
					"order_id"=> $this->getOrderID(),
					"po_number"=> $this->getPoNumber(),
					"ip_address"=> $this->customer->getIP(),
					"email_reciept"=> "false",			

					'payment_method' => array(
						'card' => array(
							"entry_type"=> $this->card->getEntryType(),
							"number"=> $this->card->getCardNumber(),
							"expiration_date"=> $this->card->getExpirationDate(),
							"cvc"=> $this->card->getCardCvc(),
							),
						),        
					'billing_address' => array(
						"first_name"=> $this->customer->getFirstName(),
						"last_name"=> $this->customer->getLastName(),
						"company"=> $this->customer->getCompany(),
						"address_line_1"=> $this->customer->getAddress(),
						"city"=> $this->customer->getCity(),
						"state"=> $this->customer->getState(),
						"postal_code"=> $this->customer->getZip(),
						"country"=> $this->customer->getCountry(),
						"phone"=> $this->customer->getPhone(),
						"fax"=> $this->customer->getFax(),
						"email"=> $this->customer->getEmail()
					),
					'shipping_address' => array(
						"first_name"=> $this->shipping_address->getFirstName(),
						"last_name"=> $this->shipping_address->getLastName(),
						"company"=> $this->shipping_address->getCompany(),
						"address_line_1"=> $this->shipping_address->getAddress(),
						"city"=> $this->shipping_address->getCity(),
						"state"=> $this->shipping_address->getState(),
						"postal_code"=> $this->shipping_address->getZip(),
						"country"=> $this->shipping_address->getCountry(),
						"phone"=> $this->shipping_address->getPhone(),
						"fax"=> $this->shipping_address->getFax(),
						"email"=> $this->shipping_address->getEmail(),
					),
			
			);

    Logger::getInstance()->write($request, Logger::DEBUG, get_class() . '::' . __FUNCTION__);
    return $request;

  }



}
?>
