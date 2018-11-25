<?php
namespace ServiceProvider;

class Response extends ResponseBase {

  public function isSuccess() {
    return $this->getStatus() == 'success';
  }

  public function isFailed() {
    return $this->getStatus() == 'failure';
  }

  public function isIncomplete() {
    return $this->getStatus() == 'incomplete';
  }

  public function isPending() {
    return $this->getStatus() == 'pending';
  }

  public function isTest() {
    if ($this->hasTransactionSection()) {
      return $this->getResponse()->transaction->test == true;
    }
    return false;
  }

  public function getStatus() {
    if ($this->hasTransactionSection()) {
      return $this->getResponse()->status;
    }elseif ($this->isError()) {
      return 'error';
    }
    return false;
  }

  public function getOrderStatus() {
    if ($this->hasTransactionDataSection()) {
      return $this->getResponse()->data->status;
    }elseif ($this->isError()) {
      return 'error';
    }
    return false;
  }


  public function getUid() {
    if ($this->hasTransactionSection()) {
          if($this->getResponse()->data !=null){
             return $this->getResponse()->data->user_id;
           }

    }else{
         return false;
    }
  }

  public function getTrackingId() {
    if ($this->hasTransactionSection()) {
          if($this->getResponse()->data !=null){
             return $this->getResponse()->data->id;
           }
    }else{
      return false;
    }
  }

 public function getTransactionId() {
    if ($this->hasTransactionSection()) {
          if($this->getResponse()->data !=null){
             return $this->getResponse()->data->id;
           }
    }else{
      return false;
    }
  }

  public function getPaymentMethod() {
    if ($this->hasTransactionSection()) {
      return $this->getResponse()->transaction->payment_method_type;
    }else{
      return false;
    }
  }

  public function hasTransactionSection() {   
    return (is_object($this->getResponse()) && isset($this->getResponse()->status));
  }

    public function hasTransactionDataSection() {   
    return (is_object($this->getResponse()) && isset($this->getResponse()->status)  && isset($this->getResponse()->data->status));
  }

  public function getMessage() {

    if (is_object($this->getResponse())) {

      if (isset($this->getResponse()->msg)) {

        return $this->getResponse()->msg;

      }
    }

    return '';

  }
}
?>
